<?php

namespace App\Services\Admin;

use App\Repositories\Admin\HomeSectionRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeSectionService
{
    use ApiResponseAble;

    public function __construct(
        private HomeSectionRepository $homeSectionRepository
    )
    {}

    public function index($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $sections = $this->homeSectionRepository->index($request);
            if (! $sections)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($sections);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeSection($request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $section = $this->homeSectionRepository->storeSection($request);
            if (! $section)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->showResponse($section);
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
            $section = $this->homeSectionRepository->show($id);
            if (! $section)
                return $this->ApiErrorResponse(null, 'This id not found');

            DB::commit();
            return $this->showResponse($section);
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateSection($request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $section = $this->homeSectionRepository->updateSection($request, $id);
            if (! $section)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse($section, "Updated Successfully");
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
            $section = $this->homeSectionRepository->changeStatus($request, $id);
            if (! $section)
                return $this->ApiErrorResponse();

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
            $sections = $this->homeSectionRepository->changeOrder($request);
            if (! $sections)
                return $this->ApiErrorResponse();

            DB::commit();
            return $this->ApiSuccessResponse(null, "Order Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteSection($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $section = $this->homeSectionRepository->deleteSection($id);
            if (! $section)
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
