<?php

namespace App\Repositories\Admin;

use App\Enums\GeneralStatusEnum;
use App\Enums\InvoiceType;
use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Models\OrderProduct;
use App\Repositories\Admin\OrderProductSerialRepository;
use App\Services\General\OnlineShoppingIntegration\IntegrationServiceFactory;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderProductRepository extends BaseRepository
{

    public function __construct(
        Application $app,
        private ProductRepository               $productRepository,
        private OrderProductOptionRepository    $orderProductOptionRepository,
        private VendorProductRepository         $vendorProductRepository,
        private IntegrationRepository           $integrationRepository,
        private ValueAddedTaxRepository         $valueAddedTaxRepository,
        private InvoiceRepository               $invoiceRepository,
        private ProductSerialRepository         $productSerialRepository,
        private OrderProductSerialRepository    $orderProductSerialRepository,
        private DirectPurchaseRepository        $directPurchaseRepository,
    )
    {
        parent::__construct($app);
    }

    public function store($orderProductData)
    {
        return $this->model->create($orderProductData);
    }

    public function changeTopUpStatusByOrderId($orderId)
    {
        return $this->model->where([
                ['order_id', $orderId],
                ['type', OrderProductType::getTypeTopUp()],
                ['status', OrderProductStatus::getTypeWaiting()],
            ])
            ->whereDoesntHave('topupTransaction')
            ->update([
                'status' => OrderProductStatus::getTypeInProgress()
            ]);
    }

    public function show($id)
    {
        return $this->model
            ->where('id', $id)
            ->with(['order:id,owner_type,owner_id','order.owner'])
            ->first();
    }

    public function changeStatusTopUp($requestData, $id)
    {
        $orderProduct = $this->model->where([
                ['id', $id],
                ['type', OrderProductType::getTypeTopUp()],
                ['status', OrderProductStatus::getTypeInProgress()],
            ])
            ->whereDoesntHave('topupTransaction')
            ->first();

        if (! $orderProduct)
            return false;
        $orderProduct->status = $requestData->status;
        $orderProduct->save();
        return true;
    }

    public function bestSellers($requestData)
    {
        $period = $requestData->input('best_sellers_period', 'today');
        $endDate = now()->endOfDay();
        switch ($period) {
            case 'yesterday':
                $startDate = now()->subDay();
                break;
            case 'last_7_days':
                $startDate = now()->subDays(7);
                break;
            case 'last_30_days':
                $startDate = now()->subDays(30);
                break;
            case 'current_month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
            case 'previous_month':
                $startDate = now()->subMonth()->startOfMonth();
                $endDate = now()->subMonth()->endOfMonth();
                break;
            default:
                $startDate = now()->startOfDay();
                break;
        }

        return $this->model
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(orders.total) as total_amount'))
            ->whereHas('order', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'DESC')
            ->with([
                'product:id,brand_id,image,price,status,type',
                'product.brand:id,status',
            ])
            ->take(10)
            ->get();
    }



    public function model(): string
    {
        return OrderProduct::class;
    }
}
