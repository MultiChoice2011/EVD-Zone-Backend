<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\SellerGroupLevelRequest;
use App\Repositories\Admin\SellerGroupLevelRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SellerGroupLevelService
{

    use FileUpload, ApiResponseAble;

    private $sellerGroupLevelRepository;
    private $languageRepository;

    public function __construct(SellerGroupLevelRepository $sellerGroupLevelRepository, LanguageRepository $languageRepository)
    {
        $this->sellerGroupLevelRepository = $sellerGroupLevelRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Categories.
     *
     */
    public function getAllsellerGroupLevels($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $sellerGroupLevels = $this->sellerGroupLevelRepository->getAllsellerGroupLevels($request);
            if (count($sellerGroupLevels) > 0)
                return $this->listResponse($sellerGroupLevels);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     *
     * Create New SellerGroupLevel.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSellerGroupLevel(SellerGroupLevelRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $sellerGroupLevel = $this->sellerGroupLevelRepository->store($data_request);
            if ($sellerGroupLevel)
                return $this->showResponse($sellerGroupLevel);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $sellerGroupLevel = $this->sellerGroupLevelRepository->show($id);
            if ($sellerGroupLevel)
                return $this->showResponse($sellerGroupLevel);
            else
                return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update SellerGroupLevel.
     *
     * @param integer $sellerGroupLevel_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSellerGroupLevel(SellerGroupLevelRequest $request, int $sellerGroupLevel_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $sellerGroupLevel = $this->sellerGroupLevelRepository->update($data_request, $sellerGroupLevel_id);
            if ($sellerGroupLevel)
                return $this->showResponse($sellerGroupLevel);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete SellerGroupLevel.
     *
     * @param int $sellerGroupLevel_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSellerGroupLevel(int $sellerGroupLevel_id): \Illuminate\Http\JsonResponse
    {
        try {
            $sellerGroupLevel = $this->sellerGroupLevelRepository->show($sellerGroupLevel_id);
            if ($sellerGroupLevel) {
                $this->sellerGroupLevelRepository->destroy($sellerGroupLevel_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * restore SellerGroupLevel.
     *
     * @param int $sellerGroupLevel_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(int $sellerGroupLevel_id): \Illuminate\Http\JsonResponse
    {
        try {
            $sellerGroupLevel = $this->sellerGroupLevelRepository->restore($sellerGroupLevel_id);
            return $this->showResponse($sellerGroupLevel);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function update_status(Request $request, int $sellerGroupLevel_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $sellerGroupLevel = $this->sellerGroupLevelRepository->update_status($data_request, $sellerGroupLevel_id);
            if ($sellerGroupLevel)
                return $this->showResponse($sellerGroupLevel);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * trash SellerGroupLevel.
     *
     * @param int $sellerGroupLevel_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function trash()
    {

        try {
            $trashes = $this->sellerGroupLevelRepository->trash();
            if (count($trashes) > 0)
                return $this->listResponse($trashes);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
