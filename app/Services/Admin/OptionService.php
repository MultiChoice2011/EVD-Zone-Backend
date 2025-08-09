<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\OptionRequest;
use App\Models\Option;
use App\Repositories\Admin\OptionRepository;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;
use App\Helpers\FileUpload;
use Illuminate\Support\Facades\DB;

class OptionService
{

    use FileUpload, ApiResponseAble;

    private $optionRepository;
    private $languageRepository;

    public function __construct(OptionRepository $optionRepository, LanguageRepository $languageRepository)
    {
        $this->optionRepository = $optionRepository;
        $this->languageRepository = $languageRepository;
    }

    /**
     *
     * All  Options.
     *
     */
    public function getAllOptions($request): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {
            $options = $this->optionRepository->getAllOptions($request);
            if (count($options) > 0) {
                return $this->listResponse($options);
            } else {
                return $this->listResponse([]);
            }
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     *
     * Create New Option.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOption(OptionRequest $request): \Illuminate\Http\JsonResponse
    {

        $data_request = $request->all();

        try {
            $option = $this->optionRepository->store($data_request);
            if ($option)
                return $this->showResponse($option);
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $option = $this->optionRepository->show($id);
            if ($option)
                return $this->showResponse($option);
            else
                return $this->notFoundResponse();
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    /**
     * Update Option.
     *
     * @param integer $option_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOption(OptionRequest $request, int $option_id): \Illuminate\Http\JsonResponse
    {
        $data_request = $request->all();
        try {
            DB::beginTransaction();
            $option = $this->optionRepository->update($data_request, $option_id);
            if (! $option)
                return $this->ApiErrorResponse(null, __('admin.general_error'));

            DB::commit();
            return $this->showResponse($option);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    /**
     * Delete Option.
     *
     * @param int $option_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOption(int $option_id): \Illuminate\Http\JsonResponse
    {
        try {
            $option = $this->optionRepository->show($option_id);
            if ($option) {
                $this->optionRepository->destroy($option_id);
                return $this->ApiSuccessResponse([], 'Success');
            }
            return $this->notFoundResponse();

        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
}
