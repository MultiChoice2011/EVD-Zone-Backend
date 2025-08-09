<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\SellerGroupRequest;
use App\Repositories\Admin\SellerGroupRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SellerGroupService
{

    use FileUpload, ApiResponseAble;

    private $sellerGroupRepository;
    private $languageRepository;

    public function __construct(SellerGroupRepository $sellerGroupRepository, LanguageRepository $languageRepository)
    {
        $this->sellerGroupRepository = $sellerGroupRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Categories.
     *
     */
    public function getAllsellerGroups($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $sellerGroups = $this->sellerGroupRepository->getAllsellerGroups($request);
            if (count($sellerGroups) > 0)
                return $this->listResponse($sellerGroups);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     *
     * Create New SellerGroup.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSellerGroup(SellerGroupRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $sellerGroup = $this->sellerGroupRepository->store($data_request);
            if ($sellerGroup)
                return $this->showResponse($sellerGroup);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $sellerGroup = $this->sellerGroupRepository->show($id);
            if ($sellerGroup)
                return $this->showResponse($sellerGroup);
            else
                return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update SellerGroup.
     *
     * @param integer $sellerGroup_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSellerGroup(SellerGroupRequest $request, int $sellerGroup_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $sellerGroup = $this->sellerGroupRepository->update($data_request, $sellerGroup_id);
            if ($sellerGroup)
                return $this->showResponse($sellerGroup);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete SellerGroup.
     *
     * @param int $sellerGroup_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteSellerGroup(int $sellerGroup_id): \Illuminate\Http\JsonResponse
    {
        try {
            $sellerGroup = $this->sellerGroupRepository->show($sellerGroup_id);
            if ($sellerGroup) {
                $this->sellerGroupRepository->destroy($sellerGroup_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
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
            $sellerGroup = $this->sellerGroupRepository->restore($sellerGroup_id);
            return $this->showResponse($sellerGroup);

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function update_status(Request $request, int $sellerGroup_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $sellerGroup = $this->sellerGroupRepository->update_status($data_request, $sellerGroup_id);
            if ($sellerGroup)
                return $this->showResponse($sellerGroup);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function auto_assign(Request $request, int $sellerGroup_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();

        try {
            $sellerGroup = $this->sellerGroupRepository->auto_assign($data_request, $sellerGroup_id);
            if ($sellerGroup)
                return $this->showResponse($sellerGroup);
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
            $trashes = $this->sellerGroupRepository->trash();
            if (count($trashes) > 0)
                return $this->listResponse($trashes);
            else
                return $this->listResponse([]);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function destroy_selected(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data_request = $request->all();
            $data_request = explode(',', $data_request['seller_groups_ids']);
            $this->sellerGroupRepository->destroy_selected($data_request);

            return $this->ApiSuccessResponse([], 'Success');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }
}
