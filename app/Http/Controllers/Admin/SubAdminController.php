<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubAdminRequests\SubAdminRequest;
use App\Http\Requests\Admin\SubAdminRequests\SubAdminStatusRequest;
use App\Services\Admin\SubAdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubAdminController extends Controller
{
    public $subAdminService;

    public function __construct(SubAdminService $subAdminService)
    {
        $this->subAdminService = $subAdminService;
    }

    public function getAdmins(Request $request)
    {
        return $this->subAdminService->getAdmins($request);
    }
    public function getOneAdmin($adminId)
    {
        return $this->subAdminService->getOneAdmin($adminId);
    }
    public function storeAdmin(SubAdminRequest $request)
    {
        return $this->subAdminService->storeAdmin($request);
    }
    public function updateAdmin(SubAdminRequest $request, $id)
    {
        return $this->subAdminService->updateAdmin($request, $id);
    }
    public function changeStatus(SubAdminStatusRequest $request, int $id)
    {
        return $this->subAdminService->changeStatus($request, $id);
    }
    public function deleteAdmin($id)
    {
        return $this->subAdminService->deleteAdmin($id);
    }



}
