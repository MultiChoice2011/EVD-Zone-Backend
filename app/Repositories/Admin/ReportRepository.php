<?php
namespace App\Repositories\Admin;

use App\Enums\OrderStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function __construct(){}
    public function getCustomerReportMovementOfPoints($request)
    {
        $disabledPagination = $request->input('disabled_pagination', 0);
        $perPage = $request->input("perPage", PAGINATION_COUNT_ADMIN);
        $sort_direction = $request->input('sort_direction','desc');
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);
        $searchTerm = $request->input('search', null);
        $type = $request->input('type',null);
        $allowedSortBy = ['order_date', 'order_number','order_total','customer_name','customer_phone'];
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'order_date';
        $reports = DB::table('points_history')
            ->join('customers', 'points_history.pointable_id', '=', 'customers.id')
            ->join('orders', 'points_history.order_id', '=', 'orders.id')
            ->select([
                'orders.created_at as order_date',
                'orders.id as order_number',
                'orders.total as order_total',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                'points_history.points_before as customer_points_before_ordering',
                'points_history.points_after as customer_points_after_ordering',
                'points_history.action_type',
                'points_history.points as order_points',
            ])
            ->when($searchTerm, function ($query) use($searchTerm) {
                return $query->where('customers.name', 'like', '%' . $searchTerm . '%');
            })
            ->when($type, function ($query)use($type) {
                return $query->where('points_history.action_type',$type);
            })
            ->when($dateFrom && $dateTo, function ($query) use($dateFrom, $dateTo) {
                $dateFrom = Carbon::parse($dateFrom)->startOfDay();
                $dateTo = Carbon::parse($dateTo)->endOfDay();
                $query->whereBetween('points_history.created_at', [$dateFrom, $dateTo]);
            })
            ->orderBy($sortBy, $sort_direction)
            ->where('orders.status',OrderStatus::COMPLETED);
            // ->where('orders.type',OrderType::POINT);
            if ($disabledPagination == 1) {
                $reportData = $reports->get();
            } else {
                $reportData = $reports->paginate($perPage);
            }
            return $reportData;
    }
    public function getCustomerPoints($request)
    {
        $disabledPagination = $request->input('disabled_pagination', 0);
        $perPage = $request->input("per_Page", PAGINATION_COUNT_ADMIN);
        $sort_direction = $request->input('sort_direction','desc');
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);
        $searchTerm = $request->input('search', null);
        $allowedSortBy = ['joining_date', 'total_points_earned','total_points_lost','name','phone','current_points_balance'];
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'joining_date';
        $query = DB::table('customers')
            ->leftJoin('points_history', 'customers.id', '=', 'points_history.pointable_id')
            ->select(
                'customers.name',
                'customers.phone',
                'customers.created_at as joining_date',
                DB::raw("SUM(CASE WHEN points_history.action_type = 'add' THEN points_history.points ELSE 0 END) as total_points_earned"),
                DB::raw("SUM(CASE WHEN points_history.action_type = 'deduct' THEN points_history.points ELSE 0 END) as total_points_lost"),
                DB::raw("
                SUM(CASE WHEN points_history.action_type = 'add' THEN points_history.points ELSE 0 END) -
                SUM(CASE WHEN points_history.action_type = 'deduct' THEN points_history.points ELSE 0 END) as current_points_balance"
                ),
            )
            ->groupBy('customers.id','customers.name','customers.phone','customers.created_at')
            ->when($searchTerm, function ($query) use($searchTerm) {
                $query->where(function($query) use ($searchTerm) {
                    $query->where('customers.name',  'like', '%' . $searchTerm . '%')
                        ->orWhere('customers.phone',  'like', '%' . $searchTerm . '%');
                });
            })
            ->when($dateFrom && $dateTo, function ($query) use($dateFrom, $dateTo) {
                $dateFrom = Carbon::parse($dateFrom)->startOfDay();
                $dateTo = Carbon::parse($dateTo)->endOfDay();
                $query->whereBetween('customers.created_at', [$dateFrom,$dateTo]);
            })
            ->orderBy($sortBy, $sort_direction);
            $totalsQuery = clone $query;
            $totals = DB::table(DB::raw("({$totalsQuery->toSql()}) as sub"))
                ->mergeBindings($totalsQuery)
                ->select(
                    DB::raw('ROUND(SUM(sub.total_points_earned), 2) as total_points_earned'),
                    DB::raw('ROUND(SUM(sub.total_points_lost), 2) as total_points_lost'),
                    DB::raw('ROUND(SUM(sub.current_points_balance), 2) as current_points_balance'),
                )
                ->first();
                if ($disabledPagination == 1) {
                    $reportData = $query->get();
                } else {
                    $reportData = $query->paginate($perPage);
                }
            return [
                'data' => $reportData,
                'totals' => [
                    'total_points_earned' => $totals->total_points_earned ?? 0,
                    'total_points_lost' => $totals->total_points_lost ?? 0,
                    'current_points_balance' => $totals->current_points_balance ?? 0,
                ],
            ];
    }
    public function getPaymentsReport($request)
    {
        $disabledPagination = $request->input('disabled_pagination', 0);
        // Assuming these are the input parameters from a request
        $langCode = $request->header('lang', 'en'); // Default to 'en' if no header is provided
        // Fetch lang_id from the languages table
        $langId = DB::table('languages')
            ->where('code', $langCode)
            ->value('id'); // Assuming the primary key in languages is 'id'
        $dateFrom = $request->input('date_from', null); // e.g., "2024-01-01"
        $dateTo = $request->input('date_to', null);     // e.g., "2024-12-31"
        $searchTerm = $request->input('search', null);
        $paymentMethod = $request->input('payment_method',null);
        $allowedPaymentBrands = ['VISA' ,'MADA' ,'STC_PAY' ,'MASTER'];
        $paymentBrand = in_array($request->input('payment_brand'), $allowedPaymentBrands) ? $request->input('payment_brand') : null;
        // Define allowed columns for sorting
        $allowedSortBy = ['created_at', 'order_number', 'payment_method','customer_name','customer_phone','product_name','cost_sold','total', 'profit', 'payment_brand','reference_number'];
        // Default to 'created_at' if invalid 'sort_by' input is given
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $query = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('product_translations', function ($join) use ($langId) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.language_id', '=', $langId);
            })
            ->join('sellers', 'orders.owner_id', '=', 'sellers.id') // Assuming there's a customers table
            ->leftJoin('order_payment_transactions', 'orders.id', '=', 'order_payment_transactions.order_id') // Join with payment transactions
            ->leftJoin('bank_commission_translations', 'orders.payment_method', '=', 'bank_commission_translations.name')
            ->leftJoin('bank_commission_settings', function ($join) {
                $join->on(DB::raw("
        CASE
            WHEN order_payment_transactions.paymentBrand = 'APPLEPAY - MADA' THEN 'MADA'
            WHEN order_payment_transactions.paymentBrand = 'APPLEPAY - MASTER' THEN 'MASTER'
            ELSE order_payment_transactions.paymentBrand
        END
    "), '=', 'bank_commission_settings.name')
                    ->whereRaw('bank_commission_settings.id = (SELECT MIN(id) FROM bank_commission_settings WHERE bank_commission_settings.name =
    (CASE
        WHEN order_payment_transactions.paymentBrand = "APPLEPAY - MADA" THEN "MADA"
        WHEN order_payment_transactions.paymentBrand = "APPLEPAY - MASTER" THEN "MASTER"
        ELSE order_payment_transactions.paymentBrand
    END))');
            })
            ->where('order_products.status', '=', OrderStatus::COMPLETED)
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $dateFrom = Carbon::parse($dateFrom)->startOfDay();
                $dateTo = Carbon::parse($dateTo)->endOfDay();
                $query->whereBetween('orders.created_at', [$dateFrom, $dateTo]);
            })
            ->when($paymentBrand, function ($query) use ($paymentBrand) {
                $query->whereNotNull('order_payment_transactions.id')
                    ->where('order_payment_transactions.paymentBrand','like','%' . $paymentBrand . '%'); // Filter by order type
            })
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('orders.payment_method', $paymentMethod);
            })
            ->when($searchTerm, function ($query) use($searchTerm) {
                $query->where(function($subQuery) use ($searchTerm) {
                    $subQuery->where('product_translations.name','like','%' . $searchTerm . '%')
                        ->orWhere('orders.id', $searchTerm)
                        ->orWhere('sellers.name','like','%' . $searchTerm . '%')
                        ->orWhere('sellers.phone','like','%' . $searchTerm . '%')
                        ->orWhere('order_payment_transactions.paymentBrand','like','%' . $searchTerm . '%')
                        ->orWhere('order_payment_transactions.reference_number', $searchTerm);
                });
            });
        $totalsQuery = clone $query;
        $totalsCashQuery = clone $query;

        $query->select(
            'orders.created_at',
            'orders.id as order_number',
            'sellers.name as seller_name',
            'sellers.phone as seller_phone',
            'product_translations.name as product_name',
            DB::raw('ROUND(order_products.quantity * products.cost_price, 3) as cost_sold'),
            DB::raw('ROUND(order_products.quantity * order_products.unit_price, 3) as total'), // Round total to 3 decimals
            DB::raw('ROUND((order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price), 3) as profit'),
            'order_payment_transactions.payment_type as payment_type',
            'order_payment_transactions.paymentBrand as payment_brand',
            'orders.payment_method',
            'order_payment_transactions.reference_number as reference_number',
            DB::raw('
        ROUND(
            (order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value,
            3
        ) as bank_commission
    '),
            DB::raw('
        ROUND(
            ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) * (bank_commission_settings.additional_value_fees / 100),
            3
        ) as additional_value_fees
    '),
            DB::raw('
        ROUND(
            ((order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price))
            - (
                ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value)
                +
                (((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) * (bank_commission_settings.additional_value_fees / 100))
            ),
            3
        ) as net_profit
    ')
        );

        $query->orderBy($sortBy, $sortDirection);

        if ($disabledPagination == 1) {
            $reportData = $query->get();
        } else {
            $reportData = $query->paginate(PAGINATION_COUNT_ADMIN);
        }
        $totals = $totalsQuery->select(
            DB::raw('ROUND(SUM(order_products.quantity * order_products.unit_price), 3) as total_sales'),
            DB::raw('ROUND(SUM(order_products.quantity * products.cost_price), 3) as total_cost_price'),
            DB::raw('ROUND(SUM((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value), 3) as total_bank_commission'),
            DB::raw('ROUND(SUM(
            ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) * (bank_commission_settings.additional_value_fees / 100)
        ), 3) as total_additional_fees'),
            DB::raw('ROUND(SUM(
            (order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price) -
            ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) -
            ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) * (bank_commission_settings.additional_value_fees / 100)
        ), 3) as total_net_profit')
        )->first();


        $totalsCash = $totalsCashQuery->select(
            DB::raw('ROUND(SUM((order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price)), 3) as total_profit')
        )
            ->first();
        return [
            'data' => $reportData,
            'totals' => [
                'total_sales' => $totals->total_sales ?? 0,
                'total_cost_price' => $totals->total_cost_price ?? 0,
                'total_profit' => $totalsCash->total_profit ?? 0,
                'total_fees' => $totals->total_additional_fees + $totals->total_bank_commission ?? 0,
                'total_net_profit' => round($totalsCash->total_profit - ($totals->total_bank_commission + $totals->total_additional_fees),3),
            ],
        ];
    }
    public function getTotalPayments($request)
    {
        $disabledPagination = $request->input('disabled_pagination', 0);
        // Assuming these are the input parameters from a request
        $langCode = $request->header('lang', 'en'); // Default to 'en' if no header is provided
        // Fetch lang_id from the languages table
        $langId = DB::table('languages')
            ->where('code', $langCode)
            ->value('id'); // Assuming the primary key in languages is 'id'
        $dateFrom = $request->input('date_from', null); // e.g., "2024-01-01"
        $dateTo = $request->input('date_to', null);     // e.g., "2024-12-31"
        $searchTerm = $request->input('search', null);
        $paymentMethod = $request->input('payment_method',null);
        $allowedPaymentBrands = ['VISA' ,'MADA' ,'STC_PAY' ,'MASTER'];
        $paymentBrand = in_array($request->input('payment_brand'), $allowedPaymentBrands) ? $request->input('payment_brand') : null;
        // Define allowed columns for sorting
        $allowedSortBy = ['created_at','payment_method','product_name','cost_sold','total', 'profit', 'payment_brand'];
        // Default to 'created_at' if invalid 'sort_by' input is given
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? ($request->input('sort_by') === 'created_at' ? DB::raw('DATE(orders.created_at)') : $request->input('sort_by'))
            : DB::raw('DATE(orders.created_at)');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('product_translations', function ($join) use ($langId) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.language_id', '=', $langId);
            })
            ->join('brands', 'products.brand_id', '=', 'brands.id')
            ->join('brand_translations', function ($join) use ($langId) {
                $join->on('brands.id', '=', 'brand_translations.brand_id')
                    ->where('brand_translations.language_id', '=', $langId);
            })
            ->leftJoin('order_payment_transactions', 'orders.id', '=', 'order_payment_transactions.order_id')
            ->leftJoin('bank_commission_translations', 'orders.payment_method', '=', 'bank_commission_translations.name')
            ->leftJoin('bank_commission_settings', function ($join) {
                $join->on('order_payment_transactions.paymentBrand', '=', 'bank_commission_settings.name')
                    ->orWhere(function ($query) {
                        $query->where('order_payment_transactions.paymentBrand', 'APPLEPAY - MADA')
                            ->where('bank_commission_settings.name', 'MADA');
                    })
                    ->orWhere(function ($query) {
                        $query->where('order_payment_transactions.paymentBrand', 'APPLEPAY - MASTER')
                            ->where('bank_commission_settings.name', 'MASTER');
                    });
            })
            ->where('order_products.status', '=', OrderStatus::COMPLETED)
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $dateFrom = Carbon::parse($dateFrom)->startOfDay();
                $dateTo = Carbon::parse($dateTo)->endOfDay();
                // add one day for dateTo to use whereBetween
                // $dateTo = Carbon::parse($dateTo);
                // $dateTo->addDay();
                $query->whereBetween('orders.created_at', [$dateFrom, $dateTo]);
            })
            ->when($paymentBrand, function ($query) use ($paymentBrand) {
                $query->whereNotNull('order_payment_transactions.id')
                    ->where('order_payment_transactions.paymentBrand','like','%' . $paymentBrand . '%'); // Filter by order type
            })
            ->when($paymentMethod, function ($query) use ($paymentMethod) {
                $query->where('orders.payment_method', $paymentMethod);
            })
            ->when($searchTerm, function ($query) use($searchTerm) {
                $query->where(function($subQuery) use ($searchTerm) {
                    $subQuery->where('product_translations.name','like','%' . $searchTerm . '%')
                        ->orWhere('brand_translations.name','like','%' . $searchTerm . '%')
                        ->orWhere('orders.id', $searchTerm)
                        ->orWhere('order_payment_transactions.paymentBrand','like','%' . $searchTerm . '%')
                        ->orWhere('order_payment_transactions.reference_number', $searchTerm);
                });
            });
        // Clone the query to calculate totals without pagination
        $totalsQuery = clone $query;
        $totalsCashQuery = clone $query;
        $query->select(
            DB::raw('DATE(orders.created_at) as created_at'),
            'product_translations.name as product_name',
//                'order_products.coins_number as coins_number',
            'brand_translations.name as brand_name',
            DB::raw('SUM(order_products.quantity) as total_orders_count'),
            DB::raw('ROUND(SUM(order_products.quantity * products.cost_price), 3) as cost_sold'),
            DB::raw('ROUND(SUM(order_products.quantity * order_products.unit_price), 3) as total'),
            DB::raw('ROUND(SUM((order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price)), 3) as profit'),
            DB::raw('SUM(products.coins_number) as coins_number'),
            'order_payment_transactions.payment_type',
            'order_payment_transactions.paymentBrand as payment_brand',
            'orders.payment_method',
            DB::raw('
                    ROUND(
                        SUM((orders.total * (bank_commission_settings.gate_fees / 100)) + bank_commission_settings.static_value),
                        3
                    ) as bank_commission
                '),
            DB::raw('
                    ROUND(
                        SUM(
                            ((orders.total * (bank_commission_settings.gate_fees / 100)) + bank_commission_settings.static_value)
                            * (bank_commission_settings.additional_value_fees / 100)
                        ),
                        3
                    ) as additional_value_fees
                '),
            DB::raw('
                    ROUND(
                        SUM(order_products.quantity * order_products.unit_price)
                        - SUM(order_products.quantity * products.cost_price)
                        - SUM(orders.total * (bank_commission_settings.gate_fees / 100))
                        - SUM(bank_commission_settings.static_value)
                        - SUM(
                            ((orders.total * (bank_commission_settings.gate_fees / 100)) + bank_commission_settings.static_value)
                            * (bank_commission_settings.additional_value_fees / 100)
                        ),
                        3
                    ) as net_profit
                ')
        )->groupBy(
            DB::raw('DATE(orders.created_at)'),
            'products.id',
            'orders.id',
            'brand_translations.name',
            'product_translations.name',
//                'order_products.coins_number',
            'order_payment_transactions.payment_type',
            'order_payment_transactions.paymentBrand',
            'orders.payment_method',
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
        $totals = $totalsQuery->select(
            DB::raw('ROUND(SUM(order_products.quantity * order_products.unit_price), 3) as total_sales'),
            DB::raw('ROUND(SUM(order_products.quantity * products.cost_price), 3) as total_cost_price'),
            DB::raw('ROUND(SUM((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value), 3) as total_bank_commission'),
            DB::raw('SUM(products.coins_number) as total_coins'),
            DB::raw('ROUND(SUM(
                ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) * (bank_commission_settings.additional_value_fees / 100)
            ), 3) as total_additional_fees'),
            DB::raw('ROUND(SUM(
                (order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price) -
                ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) -
                ((order_products.quantity * order_products.unit_price) * (bank_commission_settings.gate_fees / 100) + bank_commission_settings.static_value) * (bank_commission_settings.additional_value_fees / 100)
            ), 3) as total_net_profit')
        )->first();


        $totalsCash = $totalsCashQuery->select(
            DB::raw('ROUND(SUM((order_products.quantity * order_products.unit_price) - (order_products.quantity * products.cost_price)), 3) as total_profit')
        )
            ->first();
        return [
            'data' => $reportData,
            'totals' => [
                'total_sales' => $totals->total_sales ?? 0,
                'total_cost_price' => $totals->total_cost_price ?? 0,
                'total_profit' => $totalsCash->total_profit ?? 0,
                'total_fees' => $totals->total_additional_fees + $totals->total_bank_commission ?? 0,
                'total_net_profit' => round($totalsCash->total_profit - ($totals->total_bank_commission + $totals->total_additional_fees),3),
                'total_coins' => $totals->total_coins ?? 0,
            ],
        ];
    }
}
