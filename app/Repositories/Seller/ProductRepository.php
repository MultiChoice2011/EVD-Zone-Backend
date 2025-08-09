<?php
namespace App\Repositories\Seller;

use App\Enums\OrderStatus;
use App\Http\Resources\Seller\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Repositories\Admin\LanguageRepository;
use App\Traits\ApiResponseAble;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    use ApiResponseAble;

    public function __construct(
        public CategoryRepository           $categoryRepository,
        public OrderProductRepository       $orderProductRepository,
        public DirectPurchaseRepository     $directPurchaseRepository,
        public LanguageRepository           $languageRepository,
    ){}

    public function productsByCategory($requestData, $category): LengthAwarePaginator
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        $perPage = $requestData->input('per_page', null);
        $searchTerm = $requestData->input('search', '');
        $brandId = $requestData->input('brand_id', null);
        // Build the base query
        $query = $this->getModel()::query();
        $query->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
            $join->on("product_translations.product_id", '=', "products.id")
                ->where("product_translations.language_id", $langId);
        });
        $query->select(
            "products.id",
            "products.brand_id",
            "products.image",
            "products.quantity",
            "products.price",
            "products.wholesale_price",
            "products.status",
            "products.type",
            "products.sku",
            "products.is_live_integration",
            "products.is_available",
        );
        $query->with(['product_images']);
        $query->where('product_translations.name', 'like', '%' . $searchTerm . '%');
        $query->whereHas('categories', function ($query) use ($category) {
            $query->where('category_id', $category->id);
        });
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }
        $query->orderBy('products.price');

        $products = $query->active()->paginate($perPage ?? PAGINATION_COUNT_SELLER);
        return $this->updateProductsAvailabilityAndOptions($products, $category);

    }

    public function getProductDetailsByCategory($id, $category)
    {
        $product = $this->getModel()::where('id', $id)
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('category_id', $category->id);
            })
            ->with([
                'brand',
                'categories',
                'product_options',
            ])
            ->active()
            ->first();
        if (!$product)
            return false;
        $product->category_id = $category->id;
        $product->makeHidden('quantity');
        $product->is_max_quantity_one = 0;
        if ($category->is_topup == 1) {
            $product->is_topup = 1;
            $product->is_max_quantity_one = 1;
        } else {
            $product->is_topup = 0;
            unset($product->product_options);
            $product->product_options = [];
        }

        return $product;
    }

    public function showProductByIdAndCategoryId($id, $categoryId)
    {
        $product = $this->getModel()::where('id', $id)
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->with([
                'brand',
                'categories',
                'product_options',
            ])
            ->active()
            ->first();
        return $product;
    }

    public function getAllProducts($request)
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        $searchTerm = strip_tags($request->input("name", null));
        $brandId = $request->input("brand_id", null);
        $categoryId = $request->input("category_id", null);
        $perPage = $request->input('per_page', null);

        return $this->getModel()::select('products.*')
            ->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
             $join->on("product_translations.product_id", '=', "products.id")
                 ->where("product_translations.language_id", $langId);
            })
            ->when($searchTerm, function ($query, $searchTerm) {
             $query->where('product_translations.name', 'like', '%' . $searchTerm . '%');
            })
            ->when($brandId, function($query) use($brandId){
             $query->where('brand_id', $brandId);
            })
            ->when($categoryId , function($query) use($categoryId) {
             $query->whereHas('categories', function($categoryQuery) use($categoryId) {
                 $categoryQuery->where('category_id', $categoryId);
             });
            })
            ->with(['brand','productCategory','categories'])
            ->orderByDesc('products.id')
            ->active()
            ->paginate($perPage ?? PAGINATION_COUNT_SELLER);
    }

    public function search($request)
    {
        try{
            // Get the search query from the request
            $searchQuery = $request->input('name');
            $products = $this->getModel()::query()
            ->with(['translations','brand','productCategory']) // Load the related translations
            ->whereHas('translations', function ($query) use ($searchQuery) {
                $query->where('name', 'like', '%' . $searchQuery . '%'); // Search in the translations
            })
            ->paginate(10);
            if($products->count() > 0){
                return $this->ApiSuccessResponse(ProductResource::collection($products));
            }
            return $this->ApiErrorResponse([],trans('seller.products.not_found'));
        }catch(\Exception $e){
            return $this->ApiErrorResponse($e->getMessage(),trans('admin.general_error'));
        }
    }

    public function updateProductQuantity($id, $quantity)
    {
        $product = $this->getModel()::where('id', $id)->first();
        $product->quantity = $product->quantity - $quantity;
        $product->save();
    }

    private function updateProductsAvailabilityAndOptions(LengthAwarePaginator $products, Category $category): LengthAwarePaginator
    {
        $products->map(function($product) use($category) {
            $directPurchase = $this->directPurchaseRepository->showByProductId($product->id);
            // check if product Priorities exist to change is_available and is_max_quantity_one
            if($directPurchase && count($directPurchase->directPurchasePriorities) > 0){
                $product->is_available = 1;
            }else{
                $product->is_max_quantity_one = 0;
            }
            // get product_options
            if ($category->is_topup == 1){
                $product->load('product_options');
            }
        });

        return $products;
    }

    public function makeProductsReport($request)
    {
        $isPaginate = $request->input('is_paginate',true); // Default to true if not provided
        $langCode = $request->header('lang', 'ar'); // Default to 'en' if no header is provided
        // Fetch lang_id from the languages table
        $langId = DB::table('languages')
            ->where('code', $langCode)
            ->value('id'); // Assuming the primary key in languages is 'id'
        $dateFrom = $request->input('date_from', null);
        $dateTo = $request->input('date_to', null);
        $brandId = $request->input('brand_id', null);
        $search = $request->input('search', null);
        $allowedSortBy = ['product_name', 'brand_name', 'order_date','qty','unit_price'];
        // Default to 'created_at' if invalid 'sort_by' input is given
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'order_date';
        // Default to descending order if no 'sort_direction' is provided
        $sortDirection = $request->input('sort_direction', 'asc');
        // Initialize the query builder
        $query = DB::table('orders')
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('products', 'order_products.product_id', '=', 'products.id')
        ->join('product_translations', function ($join) use($langId){
            $join->on('products.id', '=', 'product_translations.product_id')
            ->where('product_translations.language_id', '=', $langId);
        })
        ->leftJoin('brands', 'order_products.brand_id', '=', 'brands.id')
        ->leftJoin('brand_translations', function ($join) use($langId){
            $join->on('brands.id', '=', 'brand_translations.brand_id')
                ->where('brand_translations.language_id', '=', $langId);
        })
        ->leftJoin('value_added_taxes', 'products.tax_id', '=', 'value_added_taxes.id') // Join the value added tax table
        ->selectRaw('DISTINCT
                    orders.id as order_id,
                    orders.created_at as order_date,
                    product_translations.name as product_name,
                    brand_translations.name as brand_name,
                    order_products.quantity as qty,
                    order_products.unit_price,
                    order_products.type,
                    CASE
                        WHEN products.tax_type = "percentage" AND value_added_taxes.tax_rate IS NOT NULL THEN products.tax_amount * value_added_taxes.tax_rate
                        WHEN products.tax_type = "fixed" AND value_added_taxes.tax_rate IS NOT NULL THEN products.cost_price * value_added_taxes.tax_rate
                        ELSE 0
                    END AS calc_tax_amount,
                    (order_products.unit_price * order_products.quantity) AS total_before_tax,
                    ((order_products.unit_price * order_products.quantity) +
                     CASE
                         WHEN products.tax_type = "percentage" AND value_added_taxes.tax_rate IS NOT NULL THEN (order_products.unit_price * order_products.quantity * value_added_taxes.tax_rate)
                         WHEN products.tax_type = "fixed" AND value_added_taxes.tax_rate IS NOT NULL THEN (products.cost_price * value_added_taxes.tax_rate)
                         ELSE 0
                     END) AS total_after_tax'
            )
            ->where('orders.status',OrderStatus::COMPLETED)
            ->where('owner_id',auth('sellerApi')->user()->id)
            // Apply filters
            ->when($search,function($query) use($search){
                $query->where('product_translations.name','like', '%' . $search . '%');
            })
            ->when($brandId,function($query) use($brandId){
                $query->where('order_products.brand_id',$brandId);
            })
            ->when($dateFrom && $dateTo,function($query) use($dateFrom,$dateTo){
                $dateTo = Carbon::parse($dateTo);
                $dateTo->addDay();
                $query->whereBetween('order_products.created_at', [$dateFrom, $dateTo]);
            });
        // Aggregate totals
        $totals = DB::table('orders')
            ->join('order_products', 'orders.id', '=', 'order_products.order_id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->join('product_translations', function ($join) use ($langId) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.language_id', '=',$langId);
            })
            ->leftJoin('value_added_taxes', 'products.tax_id', '=', 'value_added_taxes.id')
            ->selectRaw('
            SUM(order_products.unit_price * order_products.quantity) AS total_price,
            SUM(
                CASE
                    WHEN products.tax_type = "percentage" AND value_added_taxes.tax_rate IS NOT NULL THEN (order_products.unit_price * order_products.quantity * value_added_taxes.tax_rate)
                    WHEN products.tax_type = "fixed" AND value_added_taxes.tax_rate IS NOT NULL THEN (products.cost_price * value_added_taxes.tax_rate)
                    ELSE 0
                END
            ) AS total_tax,
            SUM(
                (order_products.unit_price * order_products.quantity) +
                CASE
                    WHEN products.tax_type = "percentage" AND value_added_taxes.tax_rate IS NOT NULL THEN (order_products.unit_price * order_products.quantity * value_added_taxes.tax_rate)
                    WHEN products.tax_type = "fixed" AND value_added_taxes.tax_rate IS NOT NULL THEN (products.cost_price * value_added_taxes.tax_rate)
                    ELSE 0
                END
            ) AS total_after_tax
        ')
            ->where('orders.status', OrderStatus::COMPLETED)
            ->where('owner_id',auth('sellerApi')->user()->id)
            ->when($search, function ($query) use ($search) {
                $query->where('product_translations.name', 'like', '%' . $search . '%');
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->where('order_products.brand_id',$brandId);
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                $dateTo = Carbon::parse($dateTo);
                $dateTo->addDay();
                $query->whereBetween('order_products.created_at', [$dateFrom, $dateTo]);
            })
            ->first();
        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);
        if ($isPaginate == "true") {
            $result = $query->paginate(PAGINATION_COUNT_ADMIN);
        } else {
            $result = $query->get();
        }
        return [
            'data' => $result,
            'totals' => $totals,
        ];
    }


    private function getModel()
    {
        return Product::class;
    }
}
