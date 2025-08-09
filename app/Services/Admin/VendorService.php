<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\VendorRequest;
use App\Http\Resources\Admin\PaginatedResource;
use App\Http\Resources\Admin\VendorResource;
use App\Models\Vendor;
use App\Repositories\Admin\CountryRepository;
use App\Repositories\Admin\VendorRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VendorService
{

    use FileUpload, ApiResponseAble;

    private $vendorRepository;
    private $countryRepository;

    public function __construct(VendorRepository $vendorRepository, CountryRepository $countryRepository)
    {
        $this->vendorRepository = $vendorRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     *
     * All  Vendors.
     *
     */
    public function getAllVendors($request)
    {
        try {
            $vendors = $this->vendorRepository->getAllVendors($request);
            if (count($vendors) > 0)
                return $this->listResponse(VendorResource::collection($vendors)->resource);
                //return $this->listResponse(VendorResource::collection($vendors)->response()->getData()); arranged
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * Create New Vendor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeVendor(VendorRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            if (isset($request->logo))
                $request->logo_url = $request->logo;
            $vendor = $this->vendorRepository->store($request);
            if (!$vendor)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            DB::commit();
            return $this->showResponse($vendor);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $vendor = $this->vendorRepository->show($id);
            if (isset($vendor))
                return $this->showResponse(new VendorResource($vendor));

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     * Update Vendor.
     *
     * @param integer $vendor_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateVendor(VendorRequest $request, int $vendor_id, $destination = 'dashboard.vendors.index'): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $vendorUpdated = $this->vendorRepository->updateVendor($request, $vendor_id);
            if (! $vendorUpdated)
                return $this->ApiErrorResponse(null, 'This item id invalid');

            DB::commit();
            return $this->ApiSuccessResponse(null, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Vendor.
     *
     * @param int $vendor_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteVendor(int $vendor_id): \Illuminate\Http\JsonResponse
    {
        try {

            $vendor = $this->vendorRepository->show($vendor_id);
            if (!$vendor)
                return $this->notFoundResponse();
            $deleted = $this->vendorRepository->destroy($vendor_id);
            if (!$deleted)
                return $this->ApiErrorResponse(null, __('admin.related_items'));
            return $this->ApiSuccessResponse(null, 'Success');
        } catch (Exception $e) {
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
            $data_request = explode(',', $data_request['order_ids']);
            $this->vendorRepository->destroy_selected($data_request);

            return $this->ApiSuccessResponse([], 'Success');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }

    public function update_status(Request $request, int $vendor_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $vendor = $this->vendorRepository->update_status($data_request, $vendor_id);
            if ($vendor)
                return $this->showResponse($vendor);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * restore SellerGroup.
     *
     * @param int $sellerGroup_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(int $sellerGroup_id): \Illuminate\Http\JsonResponse
    {
        try {
            $vendor = $this->vendorRepository->restore($sellerGroup_id);
            return $this->showResponse($vendor);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * trash SellerGroup.
     *
     * @param int $sellerGroup_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trash()
    {

        try {
            $trashes = $this->vendorRepository->trash();
            if (count($trashes) > 0)
                return $this->listResponse($trashes);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

}
