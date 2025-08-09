<?php

namespace App\Services\Admin;

use App\Helpers\FileUpload;
use App\Http\Requests\Admin\AttributeRequest;
use App\Repositories\Admin\AttributeGroupRepository;
use App\Repositories\Admin\AttributeRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttributeService
{

    use FileUpload, ApiResponseAble;

    private $attributeGroupRepository;
    private $attributeRepository;
    private $languageRepository;

    public function __construct(AttributeRepository $attributeRepository, AttributeGroupRepository $attributeGroupRepository, LanguageRepository $languageRepository)
    {
        $this->attributeRepository = $attributeRepository;
        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Attributes.
     *
     */
    public function getAllAttributes($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $attributes = $this->attributeRepository->getAllAttributes($request);
            if (count($attributes) > 0) {
                return $this->listResponse($attributes);
            } else {
                return $this->listResponse([]);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * Create New Attribute.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAttribute(AttributeRequest $request): \Illuminate\Http\JsonResponse
    {

        $data_request = $request->all();

        try {
            $attribute = $this->attributeRepository->store($data_request);
            if ($attribute)
                return $this->showResponse($attribute);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }


    /**
     * Update Attribute.
     *
     * @param integer $attribute_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAttribute(AttributeRequest $request, int $attribute_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            $attribute = $this->attributeRepository->update($data_request, $attribute_id);
            if ($attribute)
                return $this->showResponse($attribute);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Attribute.
     *
     * @param int $attribute_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAttribute(int $attribute_id): \Illuminate\Http\JsonResponse
    {
        try {
            $attribute = $this->attributeRepository->show($attribute_id);
            if ($attribute) {
                $this->attributeRepository->destroy($attribute_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
