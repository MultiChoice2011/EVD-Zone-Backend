<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\AttributeGroupRequest;
use App\Models\AttributeGroup;
use App\Repositories\Admin\AttributeGroupRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;
use App\Helpers\FileUpload;

class AttributeGroupService
{

    use FileUpload, ApiResponseAble;

    private $attributeGroupRepository;
    private $languageRepository;

    public function __construct(AttributeGroupRepository $attributeGroupRepository, LanguageRepository $languageRepository)
    {
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  AttributeGroups.
     *
     */
    public function getAllAttributeGroups($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $attributeGroups = $this->attributeGroupRepository->getAllAttributeGroups($request);
            if (count($attributeGroups) > 0) {
                return $this->listResponse($attributeGroups);
            } else {
                return $this->listResponse([]);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * Create New AttributeGroup.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAttributeGroup(AttributeGroupRequest $request): \Illuminate\Http\JsonResponse
    {

        $data_request = $request->all();

        try {
            $attributeGroup = $this->attributeGroupRepository->store($data_request);
            if ($attributeGroup)
                return $this->showResponse($attributeGroup);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     * Update AttributeGroup.
     *
     * @param integer $attributeGroup_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAttributeGroup(AttributeGroupRequest $request, int $attributeGroup_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            $attributeGroup = $this->attributeGroupRepository->update($data_request, $attributeGroup_id);
            if ($attributeGroup)
                return $this->showResponse($attributeGroup);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete AttributeGroup.
     *
     * @param int $attributeGroup_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAttributeGroup(int $attributeGroup_id): \Illuminate\Http\JsonResponse
    {
        try {
            $attributeGroup = $this->attributeGroupRepository->show($attributeGroup_id);
            if ($attributeGroup) {
                $this->attributeGroupRepository->destroy($attributeGroup_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
