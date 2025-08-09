<?php
namespace App\Services\Seller;

use App\Models\Order;
use App\Repositories\Seller\OrderProductRepository;
use App\Repositories\Seller\OrderRepository;
use App\Repositories\Seller\ProductRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Support\Facades\DB;

class ReportService
{
    use ApiResponseAble;
    public function __construct(
        public ProductRepository $productRepository,
        public OrderRepository $orderRepository,
        public OrderProductRepository $orderProductRepository){}
    public function reportProducts($request)
    {
        $data = $this->productRepository->makeProductsReport($request);
        if (!empty($data['data'])) {
            return $this->ApiSuccessResponse([
                'data' => $data['data'],
                'totals' => $data['totals']
            ]);
        }
        return $this->listResponse([]);
    }
    public function orderProductsReport($request)
    {
        $data = $this->orderProductRepository->makeOrderProductsReport($request);
        if($data->isNotEmpty()){
            return $this->ApiSuccessResponse($data);
        }
        return $this->listResponse([]);
    }
    public function orderReport($request)
    {
        $reportData = $this->orderRepository->makeOrdersReport($request);
        if (!empty($reportData['data'])) {
            return $this->ApiSuccessResponse([
                'data' => $reportData['data'],
                'totals' => $reportData['totals']
            ]);
        }

        return $this->listResponse([]);
    }
}
