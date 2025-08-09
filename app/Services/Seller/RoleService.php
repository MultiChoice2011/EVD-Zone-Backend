<?php
namespace App\Services\Seller;

use App\Http\Resources\Seller\RoleResource;
use App\Models\Role;
use App\Repositories\Seller\RoleRepository;
use App\Traits\ApiResponseAble;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RoleService
{
    use ApiResponseAble;
    public function __construct(public RoleRepository $roleRepository){}
    public function index($request)
    {
        try {
            $roles = $this->roleRepository->getAllRoles($request);
            return $this->ApiSuccessResponse($roles, 'roles data...!');
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function store($request)
    {
        try {
            DB::beginTransaction();
            $role = $this->roleRepository->store($request);
            DB::commit();
            return $this->ApiSuccessResponse($role, 'role created...!');
        } catch (Exception $e) {
            DB::rollback();
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function show($id)
    {
        try{
            $role = Role::with(['translations','permissions'])
            ->where('guard_name','sellerApi')
            ->find($id);
            if($role)
                return $this->ApiSuccessResponse($role);
            return $this->listResponse([]);
        }catch(Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function update($request,$id)
    {
        try {
            DB::beginTransaction();
            $role = $this->roleRepository->updateRole($request, $id);
            if (!$role)
                return $this->ApiErrorResponse([], 'You cant update this id');
            DB::commit();
            return $this->ApiSuccessResponse($role, 'role updated...!');

        } catch (Exception $e) {
            DB::rollback();
            //return $this->ApiErrorResponse(null, $e);
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function deleteRole($id): JsonResponse
    {
        try {
            $role = $this->roleRepository->deleteRole($id);
            if (!$role)
                return $this->ApiErrorResponse(null, 'You cant delete this id');
            return $this->ApiSuccessResponse($role, 'role deleted...!');
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }
    public function changeStatus($request, int $id): JsonResponse
    {
        try {
            $role = $this->roleRepository->changeStatus($request, $id);
            if (! $role)
                return $this->ApiErrorResponse([], 'Role not found');
            return $this->ApiSuccessResponse([], "Status Changed Successfully");
        } catch (Exception $e) {
            return $this->ApiErrorResponse($e->getMessage(), __('admin.general_error'));
        }
    }

}
