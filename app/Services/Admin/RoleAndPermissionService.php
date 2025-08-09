<?php
namespace App\Services\Admin;

use App\Repositories\Admin\PermissionRepository;
use App\Repositories\Admin\RoleRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionService
{
    use ApiResponseAble;

    public function __construct(
        private RoleRepository $roleRepository,
        private PermissionRepository $permissionRepository
    )
    {}

    public function getPermissions()
    {
        try {
            DB::beginTransaction();

            $permissions = $this->permissionRepository->index();

            DB::commit();
            return $this->ApiSuccessResponse($permissions, 'permissions data...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function getRoles($requestData)
    {
        try {
            DB::beginTransaction();

            $roles = $this->roleRepository->getRolesPaginate($requestData);

            DB::commit();
            return $this->ApiSuccessResponse($roles, 'roles data...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function getOneRole($roleId)
    {
        try {
            DB::beginTransaction();

            $role = $this->roleRepository->getOneRole($roleId);
            if (!$role)
                return $this->ApiErrorResponse(null, 'this id not found');

            DB::commit();
            return $this->ApiSuccessResponse($role, 'role data...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function getAllRoles()
    {
        try {
            DB::beginTransaction();

            $roles = $this->roleRepository->getAllRoles();

            DB::commit();
            return $this->ApiSuccessResponse($roles, 'roles data...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function storeRole($request)
    {
        try {
            DB::beginTransaction();

            $role = $this->roleRepository->store($request);

            DB::commit();
            return $this->ApiSuccessResponse($role, 'role created...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function updateRole($request, $id)
    {
        try {
            DB::beginTransaction();

            $role = $this->roleRepository->updateRole($request, $id);
            if (!$role)
                return $this->ApiErrorResponse(null, 'You cant update this id');

            DB::commit();
            return $this->ApiSuccessResponse($role, 'role updated...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function changeStatus($request, int $id): JsonResponse
    {
        try {
            $role = $this->roleRepository->changeStatus($request, $id);
            if (! $role)
                return $this->ApiErrorResponse(null, 'May be id not found');

            return $this->ApiSuccessResponse(null, "Status Changed Successfully");
        } catch (Exception $e) {
            DB::rollBack();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

    public function deleteRole($id)
    {
        try {
            DB::beginTransaction();

            $role = $this->roleRepository->deleteRole($id);
            if (!$role)
                return $this->ApiErrorResponse(null, 'You cant delete this id');

            DB::commit();
            return $this->ApiSuccessResponse($role, 'role deleted...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

}
