<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\RoleRequest\ChangeStatusRequest;
use App\Http\Requests\Seller\RoleRequest\StoreRequest;
use App\Services\Seller\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(public RoleService $roleService){}
    public function index(Request $request)
    {
        return $this->roleService->index($request);
    }
    public function store(StoreRequest $request)
    {
        return $this->roleService->store($request);
    }
    public function show($id)
    {
        return $this->roleService->show($id);
    }
    public function update(StoreRequest $request,$id)
    {
        return $this->roleService->update($request,$id);
    }
    public function destroy($id)
    {
        return $this->roleService->deleteRole($id);
    }
    public function changeStatus($id,ChangeStatusRequest $request)
    {
        return $this->roleService->changeStatus($request,$id);
    }
}
