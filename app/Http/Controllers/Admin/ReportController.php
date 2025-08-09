<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(public ReportService $reportService){}
    public function productReportSale(Request $request)
    {
        return $this->reportService->productReportSale($request);
    }
    public function orderReportSale(Request $request)
    {
        return $this->reportService->orderReportSale($request);
    }
    public function movementOfPoints(Request $request)
    {
        return $this->reportService->getPointsMovement($request);
    }
    public function customerPoints(Request $request)
    {
        return $this->reportService->customerPoints($request);
    }
    public function paymentsReport(Request $request)
    {
        return $this->reportService->paymentsReport($request);
    }
    public function totalPaymentReport(Request $request)
    {
        return $this->reportService->totalPaymentReport($request);
    }
}
