<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Seller\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(public ReportService $reportService){}
    public function reportProducts(Request $request)
    {
        return $this->reportService->reportProducts($request);
    }
    public function orderProductsReport(Request $request)
    {
        return $this->reportService->orderProductsReport($request);
    }
    public function orderReport(Request $request)
    {
        return $this->reportService->orderReport($request);
    }
}
