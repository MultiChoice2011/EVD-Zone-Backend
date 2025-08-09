<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\DashboardService;

class DashboardController extends Controller
{
    public function __construct(public DashboardService $dashboardService){}
    public function index()
    {
        return $this->dashboardService->index();
    }
}
