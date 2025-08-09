<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolesRequests\RoleRequest;
use App\Http\Requests\Admin\RolesRequests\RoleStatusRequest;
use App\Services\Admin\RoleAndPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionController extends Controller
{

    public function __construct(private RoleAndPermissionService $roleAndPermissionService)
    {}

    public function getPermissions()
    {
        return $this->roleAndPermissionService->getPermissions();
    }
    public function getRoles(Request $request)
    {
        return $this->roleAndPermissionService->getRoles($request);
    }
    public function getOneRole($roleId)
    {
        return $this->roleAndPermissionService->getOneRole($roleId);
    }
    public function getAllRoles()
    {
        return $this->roleAndPermissionService->getAllRoles();
    }

    public function storeRole(RoleRequest $request)
    {
        return $this->roleAndPermissionService->storeRole($request);
    }
    public function updateRole(RoleRequest $request, $id)
    {
        return $this->roleAndPermissionService->updateRole($request, $id);
    }
    public function changeStatus(RoleStatusRequest $request, int $id)
    {
        return $this->roleAndPermissionService->changeStatus($request, $id);
    }
    public function deleteRole($id)
    {
        return $this->roleAndPermissionService->deleteRole($id);
    }



}
