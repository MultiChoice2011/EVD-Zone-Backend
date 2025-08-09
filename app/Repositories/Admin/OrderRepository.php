<?php

namespace App\Repositories\Admin;

use App\Enums\OrderProductStatus;
use App\Enums\OrderProductType;
use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Helpers\FileUpload;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Traits\ApiResponseAble;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderRepository extends BaseRepository
{
    use FileUpload, ApiResponseAble;


    public function __construct(
        Application                          $app,
        private OrderProductRepository       $orderProductRepository,
        private ProductRepository            $productRepository,
        private ValueAddedTaxRepository      $valueAddedTaxRepository,
        private CurrencyRepository           $currencyRepository,
    )
    {
        parent::__construct($app);
    }


    public function getAllOrders($requestData, $orderProductType = null)
    {
        $authAdmin = Auth::guard('adminApi')->user();
        $status = in_array($requestData->input('status'), OrderProductStatus::getList()) ? $requestData->input('status') : '';
        $searchTerm = $requestData->input('search'); // Get the search term from the request
        $orders = $this->model->query();
        $orders->orderBy('created_at', 'desc')
            ->with([
                'user:id,name',
                'userPulled:users.id,users.name',
                'owner:id,name,phone,email',
                'order_products.product:id,brand_id',
                'order_products.brand',
                'order_products.vendor:id,name',
                'order_histories',
                'currency'
            ]);

        if ($status == OrderProductStatus::getTypeInProgress()) {
            $orders->whereHas('userPulled', function ($query) use ($authAdmin) {
                $query->where('users.id', $authAdmin->id)->select('users.id', 'users.name');
            });
        }

        $orders->whereHas('order_products', function ($query) use ($status, $orderProductType) {
            if ($orderProductType) {
                $query->where('type', $orderProductType);
            }
            if ($status){
                if ($status == OrderProductStatus::getTypeWaiting()) {
                    $query->whereDoesntHave('topupTransaction')->where('type', OrderProductType::getTypeTopUp());
                }
                $query->where('status', $status);
            }
        });
        // Apply search filters
        if ($searchTerm) {
            $orders->where(function ($query) use ($searchTerm) {
                $query->where('id', 'LIKE', "%$searchTerm%") // Search by order number
                    ->orWhereHas('owner', function ($ownerQuery) use ($searchTerm) {
                        $ownerQuery->where('phone', 'LIKE', "%$searchTerm%") // Search by phone
                            ->orWhere('name', 'LIKE', "%$searchTerm%"); // Search by name
                    });
            });
        }
        // Paginate the result
        $orders = $orders->paginate(PAGINATION_COUNT_ADMIN);
        return $orders;
    }

    public function waitingOrdersCount()
    {
        return $this->model
        ->whereStatus(OrderStatus::COMPLETED)
        ->whereHas('order_products', function ($query) {
            return $query->where('status', OrderProductStatus::getTypeWaiting());
        })->count();
    }

    public function store($requestData)
    {
        // get default currency
        $defaultCurrency = $this->currencyRepository->defaultCurrency();
        // store new order
        return $this->model->create([
            'user_id' => $requestData['user_id'],
            'status' => $requestData['order_status'],
            'owner_type' => $requestData['owner_type'],
            'owner_id' => $requestData['owner_id'],
            'currency_id' => $defaultCurrency ? $defaultCurrency->id : null,
            'payment_method' => $requestData['payment_method'],        // will handle later, Don't forget.
            'order_source' => OrderSource::getSourceDashboard(),
            'total' => $requestData->total ?? null,
            'sub_total' => $requestData->sub_total ?? null,
            'tax' => $requestData->tax ?? null
        ]);

    }

    public function show($id)
    {
        $order = $this->model
            ->with([
                'currency:id',
                'owner:id,name,email,phone',
                // 'customer.customerGroup:id',
                'user:id,name',
                'userPulled:users.id,users.name',
                'orderPaymentReceipt:id,file_path,order_id',
                'order_products.brand:id',
                'order_products.orderProductSerials',
                'order_products.product:id,image',
                'order_products.topupTransaction',
                // 'order_products.options.optionDetails:options.id,key,type',
                // 'order_products.options.optionValues.optionValueDetails:id,key',
            ])
            ->where('id', $id)
            ->first();
        if (! $order)
            return false;
        // return $order;
        return $this->formatOrderProductsWithoutHashedTopUp($order);
    }

    public function orderById($id)
    {
        return $this->model->where('id', $id)->lockForUpdate()->first();
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function update_status($data_request, $order_id)
    {
        $order = $this->model->find($order_id);
        $order->update($data_request);
        return $order->load('owner', 'order_products.product', 'order_products.brand', 'order_products.vendor',
            'order_products.product.productSerials', 'order_histories');

    }

    public function save_notes($data_request)
    {
        $order = $this->model->find($data_request['order_id']);
        $order->status = $data_request['status'];
        $order->save();
        OrderHistory::create([
            'order_id' => $order->id,
            'status' => $data_request['status'],
            'note' => $data_request['note'] ?? null,
        ]);
        return $order->load('owner', 'order_products.product', 'order_products.brand', 'order_products.vendor',
            'order_products.product.productSerials', 'order_histories');

    }

    public function get_status($data_request, $status)
    {
        return $this->model->with(['owner', 'order_products.product', 'order_products.brand', 'order_products.vendor',
            'order_products.product.productSerials', 'order_histories'])->where('status',$status)->get();

    }

    public function get_customer_orders($customer_id)
    {
        return $this->model->with(['owner', 'order_products.product', 'order_products.brand', 'order_products.vendor',
            'order_products.product.productSerials', 'order_histories'])->where('customer_id',$customer_id)->get();

    }

    public function ordersCount()
    {
        return $this->model
        ->where('created_at', '>=', now()->subDay())
        ->count();
    }

    public function ordersTotalLastDay()
    {
        return $this->model
            ->where('created_at', '>=', now()->subDay())
            ->sum('total');
    }

    public function destroy_selected($ids)
    {

        foreach ($ids as $id) {
            $order = $this->model->findOrFail($id);
            if ($order)
                $order->delete();
        }
        return true;
    }

    public function trash()
    {
        return $this->model->onlyTrashed()->get();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)->restore();

    }

    /////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////// Assets////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////
    public function formatOrderProductsWithHashedAll($order)
    {
        $order->order_products->map(function ($orderProduct) {
            $orderProduct->is_manual = 0;
            // if ($orderProduct->type == $orderProduct->orderProductSerials){
            if ($orderProduct->type == OrderProductType::getTypeTopUp() && $orderProduct->orderProductSerials){
                $orderProduct->is_manual = 1;
                $orderProduct->orderProductSerials->map(function ($orderProductSerial) {
                    $orderProductSerial->serial = $this->maskExceptLastN($orderProductSerial->serial, 3);
                    $orderProductSerial->scratching = $this->maskExceptLastN($orderProductSerial->scratching, 3);
                    return $orderProductSerial;
                });
            }
            elseif ((in_array($orderProduct->type, [OrderProductType::getTypeSerial(), OrderProductType::getTypeGift()])) && $orderProduct->orderProductSerials){
                $orderProduct->orderProductSerials->map(function ($orderProductSerial) {
                    $orderProductSerial->serial = $this->maskExceptLastN($orderProductSerial->serial, 3);
                    $orderProductSerial->scratching = $this->maskExceptLastN($orderProductSerial->scratching, 3);
                    return $orderProductSerial;
                });
            }
            return $orderProduct;

        });

        return $order;
    }

    public function formatOrderProductsWithoutHashedTopUp($order)
    {
        $order->order_products->map(function ($orderProduct) {
            $orderProduct->is_manual = 0;
            if ($orderProduct->type == OrderProductType::getTypeTopUp()){
                $orderProduct->load([
                    'options.optionDetails:options.id,key,type',
                    'options.optionValues.optionValueDetails:id,key',
                ]);
            }
            if ($orderProduct->type == OrderProductType::getTypeTopUp() && in_array($orderProduct->status, [OrderProductStatus::getTypeWaiting(), OrderProductStatus::getTypeInProgress()])
            ){
                $orderProduct->is_manual = 1;
            }
            elseif ($orderProduct->type == OrderProductType::getTypeSerial() && $orderProduct->orderProductSerials){
                $orderProduct->orderProductSerials->map(function ($orderProductSerial) {
                    $orderProductSerial->serial = $this->maskExceptLastN($orderProductSerial->serial, 3);
                    $orderProductSerial->scratching = $this->maskExceptLastN($orderProductSerial->scratching, 3);
                    return $orderProductSerial;
                });
            }
            return $orderProduct;

        });

        return $order;
    }
    public function makeOrdersSaleReport($request)
    {
        $disabledPagination = $request->input('disabled_pagination', 0);
        $langCode = $request->header('lang', 'en');
        $langId = DB::table('languages')
            ->where('code', $langCode)
            ->value('id');
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);
        $userId = $request->input('user_id', null);
        $searchTerm = $request->input('search', null);
        // Define allowed columns for sorting
        $allowedSortBy = ['created_at', 'order_number','user_name', 'seller_name', 'seller_phone', 'product_name', 'unit_price', 'cost_price', 'qty', 'total', 'profit'];
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'created_at';
        // Default to descending order if no 'sort_direction' is provided
        $sortDirection = $request->input('sort_direction', 'desc');

        $query = DB::table('orders')
            ->join('sellers','orders.owner_id','sellers.id')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('currencies', 'orders.currency_id', '=', 'currencies.id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('product_translations', function ($join)use($langId) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.language_id', '=', $langId);
            })
            ->join('brands', 'order_products.brand_id', '=', 'brands.id')
            ->join('brand_translations', function ($join)use($langId) {
                $join->on('brands.id', '=', 'brand_translations.brand_id')
                    ->where('brand_translations.language_id', '=', $langId);
            })
            ->leftJoin('order_users', 'orders.id', '=', 'order_users.order_id')
            ->leftJoin('users', 'users.id', '=', 'order_users.user_id')
            ->where('orders.status', '=', OrderStatus::COMPLETED)
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $dateFrom = Carbon::parse($dateFrom)->startOfDay();
                $dateTo = Carbon::parse($dateTo)->endOfDay();
                // add one day for dateTo to use whereBetween
                // $dateTo = Carbon::parse($dateTo);
                // $dateTo->addDay();
                $query->whereBetween('orders.created_at', [$dateFrom, $dateTo]);
            })
            ->when($userId, function ($query, $userId) {
                $query->where('users.id', $userId);
            })
            ->when($searchTerm, function ($query) use($searchTerm) {
                $query->where(function($subQuery) use ($searchTerm) {
                    $subQuery->where('product_translations.name', $searchTerm)
                        ->orWhere('orders.id', $searchTerm)
                        ->orWhere('users.name', $searchTerm)
                        ->orWhere('sellers.name', $searchTerm)
                        ->orWhere('sellers.phone', $searchTerm);
                });
            });

        // Clone the query to calculate totals without pagination
        $totalsQuery = clone $query;
        $query->select(
            'orders.created_at',
            'orders.id as order_number',
            'users.name as user_name',
            'sellers.name as seller_name',
            'sellers.phone as seller_phone',
            'product_translations.name as product_name',
            'brand_translations.name as brand_name',
            'order_products.unit_price as wholesale_price',
            'order_products.cost_price',
            'order_products.coins_number',
            'order_products.quantity as qty',
            'order_products.coins_number',
            DB::raw('ROUND((order_products.total_cost / currencies.value), 3) as cost'),
            DB::raw('ROUND((order_products.total / currencies.value), 3) as total'),
            DB::raw('ROUND((order_products.profit / currencies.value), 3) as profit'),
        );

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        // $paginatedData = $query->paginate(PAGINATION_COUNT_ADMIN);
        if ($disabledPagination == 1) {
            $reportData = $query->get();
        } else {
            $reportData = $query->paginate(PAGINATION_COUNT_ADMIN);
        }

        // get totals of all types
        $totals = $totalsQuery
            ->select(
                DB::raw('ROUND(SUM((order_products.total / currencies.value)), 3) as total_sales'),
                DB::raw('ROUND(SUM((order_products.total_cost / currencies.value)), 3) as total_cost'),
                DB::raw('ROUND(SUM((order_products.profit / currencies.value)), 3) as total_profit'),
            )
            ->first();

        return [
            'data' => $reportData,
            'totals' => [
                'total_sales' => $totals->total_sales ?? 0,
                'total_cost' => $totals->total_cost ?? 0,
                'total_profit' => $totals->total_profit ?? 0,
            ]
        ];
    }

//  public function formatOrderProducts($order)
//    {
//        return $order->order_products->flatMap(function ($orderProduct) {
//            $item = [
//                "id" => $orderProduct->id,
//                "product_id" => $orderProduct->product_id,
//                "type" => $orderProduct->type,
//                "unit_price" => $orderProduct->unit_price,
//                "status" => $orderProduct->status,
//                "product_name" => $orderProduct->product->name,
//                "product_image" => $orderProduct->product->image,
//                "quantity" => $orderProduct->quantity,
//                "is_manual" => 0,
//            ];
//            if (count($orderProduct->orderProductSerials) > 0){
//                return $orderProduct->orderProductSerials->map(function ($orderProductSerial) use ($orderProduct, $item) {
//                    if ($item['type'] == OrderProductType::getTypeSerial()) {
//                        $item = array_merge($item, [
//                            "product_serial_id" => $orderProductSerial->product_serial_id,
//                            "serial" => $this->maskExceptLastN($orderProductSerial->serial, 3),
//                            "scratching" => $this->maskExceptLastN($orderProductSerial->scratching, 3),
//                            "buying" => $orderProductSerial->buying,
//                            "expiring" => $orderProductSerial->expiring,
//                        ]);
//                        $item['options'] = [];
//                    } else {
//                        $item = array_merge($item, [
//                            "is_manual" => 1,
//                            "product_serial_id" => $orderProductSerial->product_serial_id,
//                            "serial" => $orderProductSerial->serial,
//                            "scratching" => $orderProductSerial->scratching,
//                            "buying" => $orderProductSerial->buying,
//                            "expiring" => $orderProductSerial->expiring,
//                        ]);
//                        $item['options'] = $orderProduct->options;
//                    }
//                    return $item;
//                });
//            }else{
//                return [
//                    array_merge($item, [
//                        "product_serial_id" => null,
//                        "serial" => null,
//                        "scratching" => null,
//                        "buying" => null,
//                        "expiring" => null,
//                        "options" => $orderProduct->options
//                    ])
//                ];
//            }
//        });
//    }

    private function maskExceptLastN($string, $keepLast)
    {
        $length = strlen($string);
        if ($keepLast >= $length) {
            return str_repeat('*', $length);
        }
        return str_repeat('*', $length - $keepLast) . substr($string, -$keepLast);
    }

    /**
     * Order Model
     *
     * @return string
     */
    public function model(): string
    {
        return Order::class;
    }
}
