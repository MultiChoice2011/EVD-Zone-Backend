<?php

namespace App\Repositories\Seller;

use App\Enums\OrderStatus;
use App\Models\OrderProduct;
use App\Repositories\Admin\OrderProductSerialRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class OrderProductRepository extends BaseRepository
{

    public function __construct(
        Application $app,
    )
    {
        parent::__construct($app);
    }

    public function store($orderProductData)
    {
        return $this->model->create($orderProductData);
    }
    public function makeOrderProductsReport($request)
    {
        $langCode = $request->header('lang', 'ar');
        $langId = DB::table('languages')
            ->where('code', $langCode)
            ->value('id');
        $isPaginate = $request->input('is_paginate',true);
        $search = $request->input('search',null);
        $type = $request->input('type',null);
        $dateFrom = $request->input('date_from',null);
        $dateTo = $request->input('date_to',null);
        $allowedSortBy = ['product_name', 'type','order_date','order_number','total','created_by'];
        // Default to 'created_at' if invalid 'sort_by' input is given
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'order_date';
        // Default to descending order if no 'sort_direction' is provided
        $sortDirection = $request->input('sort_direction', 'asc');
        $query = DB::table('orders')
        ->join('sellers', 'orders.owner_id', '=', 'sellers.id')
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('product_translations', function ($join) use($langId){
            $join->on('order_products.product_id', '=', 'product_translations.product_id')
            ->where('product_translations.language_id', '=', $langId);
        })
        ->selectRaw('DISTINCT orders.id as order_number, order_products.created_at as order_date,order_products.quantity as qty,order_products.unit_price as unit_price, product_translations.name as product_name,
        order_products.type, sellers.name as created_by')
        ->where('orders.status', OrderStatus::COMPLETED)
        ->where('owner_id',auth('sellerApi')->user()->id)
            ->when($search, function ($query) use($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sellers.name', 'like', "%$search%")
                        ->orWhere('orders.id', 'like', "%$search%")
                        ->orWhere('product_translations.name', 'like', "%$search%");
                });
            })
            ->when($type, function ($query) use($type) {
                $query->where('order_products.type', $type);
            })
            ->when($dateFrom && $dateTo, function ($query) use($dateFrom, $dateTo) {
                // add one day for dateTo to use whereBetween
                $dateTo = Carbon::parse($dateTo);
                $dateTo->addDay();
                $query->whereBetween('order_products.created_at', [$dateFrom, $dateTo]);
            });

        // Grouping and ordering
        $query->groupBy('orders.id', 'order_products.created_at', 'sellers.name', 'order_products.type','order_products.unit_price','order_products.quantity', 'product_translations.name');
        $query->orderBy($sortBy, $sortDirection);
        if($isPaginate == 'true'){
            $paginatedResults = $query->paginate(PAGINATION_COUNT_ADMIN);

            // Transform the paginated results
            $results = $paginatedResults->getCollection()->map(function ($row) {
                // Calculate totals
                $row->total = $row->qty * $row->unit_price;
                return $row;
            });

            // Update the collection in the paginator
            $paginatedResults->setCollection($results);
            return $paginatedResults;
        }else{
            $results = $query->get()->map(function ($row) {
                // Calculate totals
                $row->total = $row->qty * $row->unit_price;
                return $row;
            });

            return $results;
        }
    }

    public function model(): string
    {
        return OrderProduct::class;
    }
}
