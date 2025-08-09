<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\CreateAdminListRequest;
use App\Http\Requests\Seller\DeleteAdminSellerRequest;
use App\Http\Requests\Seller\UpdateAdminSellerRequest;
use App\Http\Requests\Seller\UpdateStatusAdmin;
use App\Services\Seller\AdminListService;
use Illuminate\Http\Request;

class AdminListController extends Controller
{
    public function __construct(public AdminListService $adminListService){}
    public function index(Request $request)
    {
        return $this->adminListService->index($request);
    }
    public function store(CreateAdminListRequest $request)
    {
        return $this->adminListService->store($request->validated());
    }
    public function show($id)
    {
        return $this->adminListService->show($id);
    }
    public function update(UpdateAdminSellerRequest $request,$id)
    {
        return $this->adminListService->update($request,$id);
    }
    public function destroy($id)
    {
        return $this->adminListService->destroy($id);
    }
    public function updateStatus($id,UpdateStatusAdmin $request)
    {
        return $this->adminListService->updateStatus($id,$request->validated());
    }
}
