<?php
namespace App\Services\Admin;

use App\Repositories\Admin\OrderRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ReportRepository;
use App\Traits\ApiResponseAble;

class ReportService
{
    use ApiResponseAble;
    public function __construct(
        public ProductRepository $productRepository,
        public OrderRepository $orderRepository,
        public ReportRepository $reportRepository){}
    public function productReportSale($request)
    {
        $data = $this->productRepository->makeProductsSaleReport($request);
        if(count($data) > 0)
            return $this->ApiSuccessResponse($data);
        return $this->listResponse([]);
    }
    public function orderReportSale($request)
    {
        try{
            $data = $this->orderRepository->makeOrdersSaleReport($request);
            if(count($data) > 0)
                return $this->ApiSuccessResponse($data);
            return $this->listResponse([]);
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function getPointsMovement($request)
    {
        try{
            $report = $this->reportRepository->getCustomerReportMovementOfPoints($request);
            if(count($report) > 0)
                return $this->ApiSuccessResponse($report);
            return $this->listResponse([]);
        }catch (\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function customerPoints($request)
    {
        try{
            $report = $this->reportRepository->getCustomerPoints($request);
            if(isset($report))
                return $this->ApiSuccessResponse($report);
            return $this->listResponse([]);
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function paymentsReport($request)
    {
        try{
            $report = $this->reportRepository->getPaymentsReport($request);
            if(isset($report))
                return $this->ApiSuccessResponse($report);
            return $this->listResponse([]);
        }catch (\Exception $exception){
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
    public function totalPaymentReport($request)
    {
        try{
            $report = $this->reportRepository->getTotalPayments($request);
            if(isset($report))
                return $this->ApiSuccessResponse($report);
            return $this->listResponse([]);
        }catch(\Exception $exception)
        {
            return $this->ApiErrorResponse($exception->getMessage(),trans('admin.general_error'));
        }
    }
}
