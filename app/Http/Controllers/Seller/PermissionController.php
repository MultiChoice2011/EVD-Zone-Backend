<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\PermissionService;

class PermissionController extends Controller
{
    public function __construct(public PermissionService $permissionService){}
    public function index()
    {
        return $this->permissionService->index();
    }
}
