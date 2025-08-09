<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\BrandRequests\BrandRequest;
use App\Http\Resources\Admin\BrandResource;
use App\Repositories\Admin\BrandRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Exceptions\ValidatorException;

class BrandService
{
    use FileUpload;

    private $brandRepository;
    use FileUpload, ApiResponseAble;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     *
     * All  Brands.
     *
     */
    public function getAllBrands($request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $brands = $this->brandRepository->getAllBrands($request);
            if (count($brands) > 0) {
                return $this->listResponse(BrandResource::collection($brands)->resource);
            } else {
                return $this->listResponse([]);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function getHomeSectionsBrands($request): JsonResponse
    {
        try {
            $brands = $this->brandRepository->getAllBrands($request);
            
            if (count($brands) > 0) {
                return $this->listResponse(BrandResource::collection($brands)->resource);
            } else {
                return $this->listResponse([]);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Create New Brand.
     *
     * @param BrandRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBrand(BrandRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $brand = $this->brandRepository->store($request);
            if (! $brand)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($brand);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Create New Brand.
     *
     * @param BrandRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $brand = $this->brandRepository->show($id);
            if (isset($brand))
                return $this->showResponse(new BrandResource($brand));

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update Brand.
     *
     * @param integer $brand_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidatorException
     */
    public function updateBrand(BrandRequest $request, int $brand_id): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $brand = $this->brandRepository->updateBrand($request, $brand_id);
            if (! $brand)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($brand);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function changeStatus($request, int $id): JsonResponse
    {
        try {
            $brand = $this->brandRepository->changeStatus($request, $id);
            if (! $brand)
                return $this->notFoundResponse();

            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Brand.
     *
     * @param int $brand_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteBrand(int $brand_id): \Illuminate\Http\JsonResponse
    {
        try {
            $brand = $this->brandRepository->show($brand_id);
            if (!$brand)
                return $this->notFoundResponse();
            $deleted = $this->brandRepository->destroy($brand_id);
            if (!$deleted)
                return $this->ApiErrorResponse(null, __('admin.related_items'));
            // $this->remove_file('brands', $brand->name);

            return $this->ApiSuccessResponse([], 'Brand deleted successfully.');
        }
        catch (QueryException $e) {
            if ($e->getCode() === '23000')
                return $this->ApiErrorResponse(null, __('admin.related_items'));
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
        catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Brand.
     *
     * @param int $brand_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_selected(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data_request = $request->all();
            $data_request = explode(',',$data_request['brand_ids']);
            $this->brandRepository->destroy_selected($data_request);

            return $this->ApiSuccessResponse([], 'Success');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }
}
