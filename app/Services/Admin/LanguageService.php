<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\LanguageRequest;
use App\Http\Resources\Admin\LanguageResource;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\Request;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Helpers\FileUpload;

class LanguageService
{
    use FileUpload;

    private $languageRepository;
    use FileUpload, ApiResponseAble;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Languages.
     *
     */
    public function getAllLanguages($request): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $languages = LanguageResource::collection($this->languageRepository->getAllLanguages($request));
            if (isset($languages) && count($languages) > 0) {
                return $this->listResponse($languages);
            } else {
                return $this->listResponse([]);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Create New Language.
     *
     * @param LanguageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLanguage(LanguageRequest $request): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $language = $this->languageRepository->store($data_request);
            if ($language)
                return $this->showResponse($language);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Create New Language.
     *
     * @param LanguageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $language = $this->languageRepository->show($id);
            if (isset($language) && count($language) > 0)
                return $this->showResponse($language);

            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Update Language.
     *
     * @param integer $language_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidatorException
     */
    public function updateLanguage(LanguageRequest $request, int $language_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->except('image');
        if (isset($request->image))
            $data_request['image'] = $request->image;

        try {
            $language = $this->languageRepository->update($data_request, $language_id);
            if ($language)
                return $this->showResponse($language);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Language.
     *
     * @param int $language_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLanguage(int $language_id): \Illuminate\Http\JsonResponse
    {
        try {
            $language = $this->languageRepository->show($language_id);
            if ($language) {
                $this->remove_file('languages', $language->name);
                $this->languageRepository->destroy($language_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }

    }
}
