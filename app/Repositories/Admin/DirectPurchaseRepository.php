<?php

namespace App\Repositories\Admin;

use App\Models\DirectPurchase;
use App\Models\DirectPurchasePriority;
use App\Models\Product;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class DirectPurchaseRepository extends BaseRepository
{
    public function __construct(
        Application                                     $app,
        private DirectPurchasePriorityRepository        $directPurchasePriorityRepository,
        private LanguageRepository                      $languageRepository

    )
    {
        parent::__construct($app);
    }


    public function index($requestData)
    {
        $perPage = $requestData->input('per_page', PAGINATION_COUNT_ADMIN);

        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        if (in_array($requestData->input('sort_by'), ['product', 'brand', 'status']))
            $sortBy = $requestData->input('sort_by');
        else
            $sortBy = 'product';

        $sortDirection = $requestData->input('sort_direction', 'desc');
        $searchTerm = $requestData->input('search', '');
        $brandIds = null;
        if ($requestData->has('brand_ids') && $requestData->input('brand_ids') != '') {
            $brandIds = explode(',', $requestData->input('brand_ids', null));
        }
        $categoryIds = null;
        if ($requestData->has('category_ids') && $requestData->input('category_ids') != '') {
            $categoryIds = explode(',', $requestData->input('category_ids', null));
        }

        // Build the base query
        $query = Product::query();
        // Join attached tables
        $query->join('vendor_products', "products.id", '=', "vendor_products.product_id");
        $query->leftJoin('direct_purchases', "products.id", '=', "direct_purchases.product_id");
        $query->leftJoin('brands', function (JoinClause $join) use ($brandIds) {
            $join->on("products.brand_id", '=', "brands.id");
            if (!empty($brandIds)) {
                $join->whereIn('products.brand_id', $brandIds);
            } else {
                $join->orWhereNull('products.brand_id');
            }
        });
        $query->leftJoin('brand_translations', function (JoinClause $join) use ($langId) {
            $join->on("brand_translations.brand_id", '=', "brands.id")
                ->where("brand_translations.language_id", $langId);
        });
        $query->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
            $join->on("product_translations.product_id", '=', "products.id")
                ->where("product_translations.language_id", $langId);
        });
        $query->select([
            'products.id',
            'products.brand_id',
            'products.image',
            // 'direct_purchases.id as direct_purchase_id',
            // 'direct_purchases.product_id',
            // 'direct_purchases.status',
            // 'direct_purchases.created_at',
            // 'direct_purchases.updated_at',
        ]);
        $query->groupBy(
            'products.id',
            'products.brand_id',
            'products.image',
            'product_translations.name',
            'brand_translations.name',
            // 'direct_purchases.id',
            // 'direct_purchases.product_id',
            // 'direct_purchases.status',
            // 'direct_purchases.created_at',
            // 'direct_purchases.updated_at'
        );
        // get attaching with product
        $query->with([
            'directPurchase',
            'directPurchase.directPurchasePriorities' => function ($directPurchasePriorittQuery) {
                $directPurchasePriorittQuery->orderBy('priority_level');
            },
            'directPurchase.directPurchasePriorities.vendor:id,name',
            'brand:id'
        ]);
        if ($categoryIds) {
            $query->whereHas('categories', function (Builder $query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            });
        }
        // Apply sorting
        if ($sortBy == 'product')
            $query->orderBy('product_translations.name', $sortDirection);
        elseif ($sortBy == 'brand')
            $query->orderBy('brand_translations.name', $sortDirection);
        else
            $query->orderBy($sortBy, $sortDirection);
        // Apply searching
        if ($searchTerm) {
            $query->where('product_translations.name', 'like', '%' . $searchTerm . '%');
        }
        // Retrieve paginated results
        return $query->paginate($perPage);
    }

    public function store($requestData)
    {
        $directPurchase = $this->model->updateOrCreate([
            'product_id' => $requestData->product_id
        ],[
            'product_id' => $requestData->product_id
        ]);

        if (! $requestData->vendor_id) {
            $this->directPurchasePriorityRepository->deletePriority($requestData, $directPurchase->id);
            return true;
        }

        $this->directPurchasePriorityRepository->store($requestData, $directPurchase->id);
        return true;
    }

    public function showByProductId($productId)
    {
        return $this->model
            ->where('product_id', $productId)
            ->with([
                'directPurchasePriorities' => function ($directPurchasePriorittQuery) {
                    $directPurchasePriorittQuery->orderBy('priority_level');
                },
            ])
            ->first();
    }

    public function changeStatus($requestData, $id)
    {
        $directPurchase = $this->model->find($id);
        if(!$directPurchase){
            return false;
        }
        // change status
        $directPurchase->status = $requestData->status;
        $directPurchase->save();

        return $directPurchase;
    }

    public function model(): string
    {
        return DirectPurchase::class;
    }
}
