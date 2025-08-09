<?php

namespace App\Services\Admin;

use App\Repositories\Admin\StaticPageRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaticPageService
{
    use ApiResponseAble;

    public function __construct(
        private StaticPageRepository $staticPageRepository
    )
    {}

    public function index()
    {
        try {
            DB::beginTransaction();
            $pages = $this->staticPageRepository->index();
            DB::commit();
            return $this->ApiSuccessResponse($pages, 'Static Pages...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();
            $page = $this->staticPageRepository->storePage($request);
            if (! $page)
                return $this->ApiErrorResponse();
            DB::commit();
            return $this->ApiSuccessResponse(null, 'Created Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function show($pageId)
    {
        try {
            DB::beginTransaction();
            $pages = $this->staticPageRepository->show($pageId);
            DB::commit();
            return $this->ApiSuccessResponse($pages, 'Static Pages...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();
            $page = $this->staticPageRepository->updatePage($request, $id);
            if (! $page)
                return $this->ApiErrorResponse(null, 'May be id not found');

            DB::commit();
            return $this->ApiSuccessResponse(null, 'Updated Successfully...!');
        } catch (Exception $e) {
            DB::rollBack();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, int $id): JsonResponse
    {
        try {
            $page = $this->staticPageRepository->changeStatus($request, $id);
            if (! $page)
                return $this->ApiErrorResponse(null, 'May be id not found');

            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function delete(int $id): JsonResponse
    {
        try {
            $pageDeleted = $this->staticPageRepository->deletePage($id);
            if (! $pageDeleted)
                return $this->ApiErrorResponse(null, 'May be id not found');

            return $this->ApiSuccessResponse(null, "Deleted Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }




}
