<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\SliderRequests\SliderRequest;
use App\Repositories\Admin\SliderRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SliderService
{
    use ApiResponseAble;

    public function __construct(
        private SliderRepository $sliderRepository
    )
    {}

    public function index($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $sliders = $this->sliderRepository->index($request);
            if (! $sliders)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($sliders);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeSlider($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $slider = $this->sliderRepository->storeSlider($request);
            if (! $slider)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($slider);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $slider = $this->sliderRepository->show($id);
            if (! $slider)
                return $this->ApiErrorResponse(null, 'This id not found');

            DB::commit();
            return $this->showResponse($slider);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateSlider($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $slider = $this->sliderRepository->updateSlider($request, $id);
            if (! $slider)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse($slider, "Updated Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $slider = $this->sliderRepository->changeStatus($request, $id);
            if (! $slider)
                return $this->ApiErrorResponse(null, 'This id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeOrder($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $sliders = $this->sliderRepository->changeOrder($request);
            if (! $sliders)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Order Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteSlider($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $slider = $this->sliderRepository->deleteSlider($id);
            if (! $slider)
                return $this->ApiErrorResponse(null, 'This id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, "Deleted.");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }



}
