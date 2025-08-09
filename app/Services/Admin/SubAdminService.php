<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SubAdminService
{
    use ApiResponseAble;

    public function __construct(private AdminRepository $adminRepository)
    {}

    public function getAdmins($requestData)
    {
        try {
            DB::beginTransaction();

            $admins = $this->adminRepository->getAdmins($requestData);

            DB::commit();
            return $this->ApiSuccessResponse($admins, 'Admins data...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function getOneAdmin($adminId)
    {
        try {
            DB::beginTransaction();

            $admin = $this->adminRepository->getOneAdmin($adminId);
            if (! $admin)
                return $this->ApiErrorResponse(null, 'this id not found');

            DB::commit();
            return $this->ApiSuccessResponse($admin, 'Admin data...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeAdmin($request)
    {
        try {
            DB::beginTransaction();

            $admin = $this->adminRepository->storeAdmin($request);

            DB::commit();
            return $this->ApiSuccessResponse($admin, 'Admin created...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateAdmin($request, $id)
    {
        try {
            DB::beginTransaction();

            $admin = $this->adminRepository->updateAdmin($request, $id);
            if (! $admin)
                return $this->ApiErrorResponse(null, 'this id not found');

            DB::commit();
            return $this->ApiSuccessResponse($admin, 'Admin updated...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, int $id): JsonResponse
    {
        try {
            $admin = $this->adminRepository->changeStatus($request, $id);
            if (! $admin)
                return $this->ApiErrorResponse(null, 'May be id not found');

            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteAdmin($id)
    {
        try {
            DB::beginTransaction();

            $admin = $this->adminRepository->deleteAdmin($id);
            if (!$admin)
                return $this->ApiErrorResponse(null, 'this id not found');

            DB::commit();
            return $this->ApiSuccessResponse($admin, 'Admin deleted...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }





}
