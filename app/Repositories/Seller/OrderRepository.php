<?php
namespace App\Repositories\Seller;

use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Seller;
use App\Repositories\Admin\CurrencyRepository;
use App\Repositories\Admin\FailedOrderReasonRepository;
use App\Repositories\Admin\OrderProductRepository;
use App\Repositories\Admin\ProductRepository;
use App\Services\General\CurrencyService;
use App\Services\Seller\SellerService;
use App\Traits\ApiResponseAble;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepository{
    use ApiResponseAble;
    public function __construct(
        public ProductRepository                $productRepository,
        public SellerService                    $sellerService,
        public OrderProductRepository           $orderProductRepository,
        public CurrencyRepository               $currencyRepository,
        public FailedOrderReasonRepository      $failedOrderReasonRepository,
        public SettingRepository                $settingRepository
    ){}
    public function getAllOrders($requestData, $sellerId)
    {
        $startDate = $requestData->input('start_date', null);
        $endDate = $requestData->input('end_date', null);
        // add one day for endDate to use whereBetween
        $endDate = Carbon::parse($endDate);
        $endDate->addDay();

        $query = $this->getModel()::query();
        $query->withCount('order_products')
            ->with([
                'currency',
                'owner:id,name,phone,email',
                'user:id,name',
            ])
            ->where('owner_type', Seller::class)
            ->where('owner_id', $sellerId)
            ->whereIn('status', [OrderStatus::PAID, OrderStatus::COMPLETED, OrderStatus::RETURNED]);

        if($startDate && $endDate){
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->orderBy('created_at', 'desc')->paginate(PAGINATION_COUNT_APP);
    }
    public function show($id, $sellerId)
    {
        $order = $this->getModel()::with([
                'currency',
                'owner:id,name,phone,email,tax_card_number,commercial_register_number',
                'owner.sellerAddress.country',
                'owner.sellerAddress.city',
                'owner.sellerAddress.region',
                'order_products.product:id,brand_id,image,tax_id,tax_type,tax_amount',
                'order_products.product.brand:id',
                'order_products.options.optionDetails:options.id,key',
                'order_products.options.optionValues.optionValueDetails:id,key',
            ])
            ->where('id', $id)
            ->where('owner_type', Seller::class)
            ->where('owner_id', $sellerId)
            ->first();
        if (! $order)
            return false;
        $orderDetails = $this->formatOrderProducts($order);
        $data = [];
        $data['setting_tax'] = $this->settingRepository->getTaxesKeys();
        $data['order_details'] = $orderDetails;

        return $data;
    }
    public function store($cart, $requestData)
    {
        // get default currency
        $defaultCurrency = $this->currencyRepository->defaultCurrency();
        // store new order
        return $this->getModel()::create([
            'owner_type' => $requestData->owner_type,
            'owner_id' => $requestData->owner_id,
            'subseller_id' => $requestData->subseller_id,
            'status' => $requestData->order_status,
            'customer_id' => $cart->owner_id,
            'currency_id' => $defaultCurrency ? $defaultCurrency->id : null,
            'payment_method' => $requestData->payment_method,        // will handle later, Don't forget.
            'total' => $cart->total_price,
            'sub_total' => $cart->cart_price,
            'tax' => $cart->tax_rate,
        ]);
    }

    public function formatOrderProducts($order)
    {
        $order->order_products->each(function ($orderProduct) {
            if($orderProduct['type'] != OrderProductType::getTypeTopUp()){
                $orderProduct->load('orderProductSerials');
            }else{
                $orderProduct['orderProductSerials'] = [];
            }
            return $orderProduct;
        });

        return $order;
    }
    public function getCountOfOrders($periodType = null, $value = null)
    {
        $authSeller = auth('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        // make query for current seller or his parent
        $query = $this->getModel()
            ->where('owner_id', $originalSeller->id);
        // check if period exist to filter by it
        if ($periodType && $value) {
            $query->where('created_at', '>=', now()->sub($periodType, $value));
        }

        return $query->count();
    }
    public function getTotalValueOfOrders($periodType = null, $value = null)
    {
        // Get the authenticated seller
        $authSeller = auth('sellerApi')->user();
        $originalSeller = SellerService::getOriginalSeller($authSeller);
        // Fetch the currency conversion rate for the authenticated seller
        $currency = CurrencyService::getCurrentCurrency($authSeller);
        // Ensure the currency conversion rate is valid; default to 1 if not
        $conversionRate = $currency->value ?? 1;
        // make query for current seller or his parent
        $query = $this->getModel()
            ->where('owner_id', $originalSeller->id);
        // check if period exist to filter by it
        if ($periodType && $value) {
            $query->where('created_at', '>=', now()->sub($periodType, $value));
        }
        // get sum of total
        $totalValue = $query->sum('total');
        return round($totalValue * $conversionRate, 3);
    }
    public function makeOrdersReport($request)
    {
        $isPaginate = $request->input('is_paginate',true);
        $search = $request->input('search',null);
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);
        $allowedSortBy = ['order_number', 'created_by', 'product_count','total'];
        // Default to 'created_at' if invalid 'sort_by' input is given
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'order_date';
        // Default to descending order if no 'sort_direction' is provided
        $sortDirection = $request->input('sort_direction', 'asc');
        $query = DB::table('orders')
        ->join('sellers', 'orders.owner_id', '=', 'sellers.id') // Assuming 'owner_id' links to the seller
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->selectRaw('
            DISTINCT orders.id as order_number,
            orders.created_at as order_date,
            orders.total as total,
            COUNT(order_products.id) as products_count,
            sellers.name as created_by
        ')
        ->where('orders.status', OrderStatus::COMPLETED)
        ->where('owner_id',auth('sellerApi')->user()->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sellers.name', 'like', "%$search%")
                        ->orWhere('orders.id', 'like', "%$search%");
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use($dateFrom, $dateTo) {
                // add one day for dateTo to use whereBetween
                $dateTo = SupportCarbon::parse($dateTo);
                $dateTo->addDay();
                $query->whereBetween('orders.created_at', [$dateFrom, $dateTo]);
            })
        ->groupBy('orders.id', 'orders.created_at','orders.total','sellers.name');
        // Get totals using a separate query
        $totals = DB::table('orders')
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->selectRaw('
            SUM(orders.total) as totalOrders,
            COUNT(order_products.id) as totalProductsCount
        ')
        ->where('orders.status', 'completed')
        ->where('owner_id',auth('sellerApi')->user()->id);
        // Apply the same filters to the totals query
        if ($request->has('date_from') && !empty($request->date_from)) {
            $totals->whereDate('orders.created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && !empty($request->date_to)) {
            $totals->whereDate('orders.created_at', '<=', $request->date_to);
        }
        if ($request->has('search') && !empty($request->search)) {
            $searchKey = $request->search;
            $totals->where(function ($q) use ($searchKey) {
                $q->where('orders.owner_id', 'like', "%$searchKey%");
            });
        }
        // Execute the totals query
        $totalsData = $totals->first();
        $query->orderBy($sortBy, $sortDirection);
        if ($isPaginate == 'true') {
            $results = $query->paginate(PAGINATION_COUNT_ADMIN);
        } else {
            $results = $query->get();
        }
        return [
            'data' => $results,
            'totals' => [
                'totalOrders' => $totalsData->totalOrders ?? 0,
                'totalProductsCount' => $totalsData->totalProductsCount ?? 0,
            ]
        ];
    }

    private function getModel()
    {
        return new Order();
    }
}
