<?php

namespace App\Repositories\Admin;

use App\Enums\GeneralStatusEnum;
use App\Enums\OrderStatus;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\ProductSerial;
use Carbon\Carbon;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Models\ProductDiscountSellerGroup;
use App\Models\ProductPriceSellerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    private $productTranslationRepository;
    private $languageRepository;
    private $productCategoryRepository;
    private $productPriceSellerGroupRepository;

    public function __construct(
        Application $app,
        ProductTranslationRepository $productTranslationRepository,
        ProductCategoryRepository $productCategoryRepository,
        LanguageRepository $languageRepository,
        ProductPriceSellerGroupRepository $productPriceSellerGroupRepository,
    )
    {
        parent::__construct($app);
        $this->productTranslationRepository = $productTranslationRepository;
        $this->languageRepository = $languageRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productPriceSellerGroupRepository = $productPriceSellerGroupRepository;
    }

    public function getAllProducts($requestData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        if (in_array($requestData->input('sort_by'), ['created_at', 'name', 'brand', 'type', 'quantity', 'status', 'price', 'cost_price', 'wholesale_price']))
            $sortBy = $requestData->input('sort_by');
        else
            $sortBy = 'created_at';

        $sortDirection = $requestData->input('sort_direction', 'desc');
        $searchTerm = $requestData->input('search', '');
        $statusFilter = in_array($requestData->input('status'), GeneralStatusEnum::getList()) ? $requestData->input('status') : null;
        $categoryIds = null;
        if ($requestData->has('category_ids') && $requestData->input('category_ids') != '') {
            $categoryIds = explode(',', $requestData->input('category_ids', null));
        }
        $brandIds = null;
        if ($requestData->has('brand_ids') && $requestData->input('brand_ids') != '') {
            $brandIds = explode(',', $requestData->input('brand_ids', null));
        }

        // Build the base query
        $query = $this->model->query();
        // Join attached table
        $query->join('brands', function (JoinClause $join) use ($brandIds) {
            $join->on("products.brand_id", '=', "brands.id");
            if (!empty($brandIds)) {
                $join->whereIn('products.brand_id', $brandIds);
            } else {
                $join->orWhereNull('products.brand_id');
            }
        });
        $query->join('brand_translations', 'brand_translations.brand_id', '=', 'brands.id')
          ->where('brand_translations.language_id', $langId); // Ensure we filter by language for brand

        $query->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
            $join->on('product_translations.product_id', '=', 'products.id')
                 ->where('product_translations.language_id', $langId); // Ensure we filter by language for products
        });
        $query->leftJoin(
            DB::raw('(SELECT product_id, SUM(quantity) as sold_quantity
                      FROM order_products
                      WHERE status = "completed"
                      GROUP BY product_id) as sold_quantities'),
            'products.id', '=', 'sold_quantities.product_id'
        );
        $query->select(
            'products.*',
            'product_translations.name as product_name',
            'brand_translations.name as brand_name',
            DB::raw('COALESCE(sold_quantities.sold_quantity, 0) as sold_quantity')
        )->distinct('products.id');
        // $query->groupBy(
        //     "id",
        //     "brand_id",
        //     "serial",
        //     "quantity",
        //     "image",
        //     "price",
        //     "cost_price",
        //     "points",
        //     "status",
        //     "sort_order",
        //     "created_at",
        //     "updated_at",
        //     "type",
        //     "vendor_id",
        //     "web",
        //     "mobile",
        //     "sku",
        //     "notify",
        //     "minimum_quantity",
        //     "max_quantity",
        //     "wholesale_price",
        //     "tax_id",
        //     "packing_method",
        //     "tax_type",
        //     "tax_amount",
        //     "is_live_integration",
        //     "is_available",
        // );

        // Apply status filter
        if ($statusFilter) {
            $query->where('products.status', $statusFilter);
        }
        // Apply categories filter
        if ($categoryIds) {
            $query->whereHas('categories', function (Builder $query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            });
        }
        // get attaching with product
        $query->with(['translations', 'product_images', 'brand', 'categories', 'vendor', 'productDiscountSellerGroup', 'productPriceSellerGroup']);
        // Apply searching
        if ($searchTerm) {
            $query->where('product_translations.name', 'like', '%' . $searchTerm . '%');
        }
        // Apply sorting
        if ($sortBy == 'name')
            $query->orderBy('product_translations.name', $sortDirection);
        elseif ($sortBy == 'brand')
            $query->orderBy('brand_translations.name', $sortDirection);
        // elseif ($sortBy == 'category')
        //     $query->orderBy('category_translations.name', $sortDirection);
        else
            $query->orderBy($sortBy, $sortDirection);
        // second ordering to ensure uniqueness
        $query->orderBy('products.id', 'desc');
        // Retrieve paginated results
        return $query->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function get_brand_products($brand_id): array|\Illuminate\Database\Eloquent\Collection
    {

        return $this->model->with(['translations', 'product_images', 'brand', 'category', 'vendor'])->where('brand_id', $brand_id)->get();
    }

    public function showProductByIdAndCategoryId($id, $categoryId)
    {
        return $this->model
            ->where('id', $id)
            ->when($categoryId, function ($query, $categoryId) {
                $query->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('category_id', $categoryId);
                });
                $query->with([
                    'product_options',
                ]);
                return $query;
            })
            ->with([
                'product_images',
                'vendor:id,name,logo',
                'brand',
                'categories',
                'product_options.product_option_value',
            ])
            ->active()
            ->first();
    }

    public function store($data_request)
    {

        $product = $this->model->create($data_request);
        if ($product) {
            $this->productTranslationRepository->store($data_request['name'], $data_request['desc'] ?? null, $data_request['meta_title'] ?? null, $data_request['meta_keyword'] ?? null, $data_request['meta_description'] ?? null, $data_request['content'] ?? null, $data_request['long_desc'] ?? null, $product->id);
            if (!empty($data_request['images'])) {
                ProductImage::where('product_id', $product->id)->delete();
                foreach ($data_request['images'] as $image) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $image,
                    ]);
                }
            }

            // save categories for product
            $product->categories()->sync($data_request['category_ids']);

            // Store product options if available
            if (!empty($data_request['product_options'])) {
                foreach ($data_request['product_options'] as $option) {
                    if (isset($option['option_id'])){

                        // Create product option
                        $productOption = ProductOption::create([
                            'product_id' => $product->id,
                            'option_id' => (int) $option['option_id'],
                            // 'value' => $option['value'],
                            'required' => $option['required'] ?? 0,
                        ]);

                        // Create product option values if available
                        if (!empty($option['values'])) {
                            foreach ($option['values'] as $value) {
                                ProductOptionValue::create([
                                    'product_option_id' => $productOption->id,
                                    'product_id' => $product->id,
                                    'option_id' => $option['option_id'],
                                    'option_value_id' => $value['option_value_id'] ?? null,
                                    'quantity' => $value['quantity'] ?? 0,
                                    'subtract' => $value['subtract'] ?? 1,
                                    'price' => $value['price'] ?? 0.00,
                                    'price_prefix' => $value['price_prefix'] ?? '+',
                                    'points' => $value['points'] ?? 0,
                                    'points_prefix' => $value['points_prefix'] ?? '+',
                                    'weight' => $value['weight'] ?? 0.00000000,
                                    'weight_prefix' => $value['weight_prefix'] ?? '+',
                                ]);
                            }
                        }
                    }
                }
            }

            // Optionally, you can save other related models here as well


            // Now, iterate over each related model and save the data
            $relatedData = [
                // ProductDiscountCustomerGroup::class => [],
                ProductDiscountSellerGroup::class => [],
                // ProductPriceCustomerGroup::class => [],
                ProductPriceSellerGroup::class => []
            ];

            foreach ($relatedData as $relatedModel => $dummyArray) {
                // Assuming your data structure matches your database columns
                $relatedModelData = $data_request[strtolower(class_basename($relatedModel))] ?? [];

                // Save related model data if available
                if (!empty($relatedModelData)) {
                    foreach ($relatedModelData as $item) {
                        // Instantiate the related model class
                        $relatedModelInstance = new $relatedModel;

                        // Fill the model with data
                        $relatedModelInstance->fill($item);

                        // Save the related model
                        $relatedModelInstance->product_id = $product->id;
                        $relatedModelInstance->save();
                    }
                }
            }
        }

      return  $product->load('translations');

    }

    public function serials($data_request)
    {
        $product = $this->model->find($data_request['product_id']);
        $product->update($data_request);
        ProductSerial::where('product_id', $product->id)->delete();
        if ($product) {
            // Now, iterate over each related model and save the data
            $relatedData = [
                ProductSerial::class => [],
            ];

            foreach ($relatedData as $relatedModel => $dummyArray) {
                // Assuming your data structure matches your database columns
                $relatedModelData = $data_request[strtolower(class_basename($relatedModel))] ?? [];

                // Save related model data if available

                // Save related model data if available
                if (!empty($relatedModelData)) {
                    foreach ($relatedModelData as $item) {
                        // Instantiate the related model class
                        $relatedModelInstance = new $relatedModel;

                        // Fill the model with data
                        $relatedModelInstance->fill($item);

                        // Save the related model
                        $relatedModelInstance->save();
                    }
                }
            }
        }

        return $product->load('translations');

    }

    public function applyPriceAll($data_request)
    {
        if (in_array($data_request['price_type'], ['cost_price', 'price', 'wholesale_price'])) {
            $this->model->query()->update([$data_request['price_type'] => $data_request['amount']
            ]);
            return true;
        }
        return false;
    }

    public function applyPriceAllGroups($data_request)
    {
        // Check if the price action is 'fixed' or 'percentage'
        // Update records for seller groups if seller_group_id is provided
        if ($data_request['seller_group_id']) {
            ProductPriceSellerGroup::query()
                ->where('seller_group_id', $data_request['seller_group_id'])
                ->update([
                    "amount_percentage" => $data_request['amount_percentage'],
                    "price" => $data_request['amount']
                ]);
        }

        // Update records for customer groups if customer_group_id is provided
        if ($data_request['customer_group_id']) {
            // ProductPriceCustomerGroup::query()
            //     ->where('customer_group_id', $data_request['customer_group_id'])
            //     ->update([
            //         "amount_percentage" => $data_request['amount_percentage'],
            //         "price" => $data_request['amount']
            //     ]);
        }

        return true;
        /*
        // Check if the price action is 'new'
        elseif ($data_request['price_action'] === 'new') {
            // Update records for seller groups if seller_group_id is provided
            if ($data_request['seller_group_id']) {
                ProductPriceSellerGroup::query()
                    ->where('seller_group_id', $data_request['seller_group_id'])
                    ->update([
                        "amount" => $data_request['amount']
                    ]);
            }

            // Update records for customer groups if customer_group_id is provided
            if ($data_request['customer_group_id']) {
                ProductPriceCustomerGroup::query()
                    ->where('customer_group_id', $data_request['customer_group_id'])
                    ->update([
                        "amount" => $data_request['amount']
                    ]);
            }

            return true;
        }*/

        return false;
    }

    public function prices($data_request)
    {
        $data = ['success' => true, 'error' => null];
        // Now, iterate over each related model and save the data
        $relatedData = [
            // ProductPriceCustomerGroup::class => [],
            ProductPriceSellerGroup::class => []
        ];

        foreach ($relatedData as $relatedModel => $dummyArray) {
            // Assuming your data structure matches your database columns
            $relatedModelData = $data_request[strtolower(class_basename($relatedModel))] ?? [];

            // Save related model data if available

            // Save related model data if available
            if (!empty($relatedModelData)) {
                foreach ($relatedModelData as $item) {
                    if ($item['price_product'] < $item['cost_price']) {
                        $data['success'] = false;
                        $data['error'] = 'price is less than cost_price';
                        return $data;
                    }
                    $product = $this->model->find($item['product_id']);
                    $product->cost_price = $item['cost_price'];
                    $product->price = $item['price_product'];
                    $product->wholesale_price = $item['wholesale_price'];
                    $product->save();
                    if (isset($data_request['productpricesellergroup']) && isset($item['seller_group']) && isset($item['product_id'])) {
                        $this->productPriceSellerGroupRepository->deleteByProductId($item['product_id']);
                        $productPriceSellerGroup = $this->productPriceSellerGroupRepository->store($item);
                        if (! $productPriceSellerGroup) {
                            $data['success'] = false;
                            $data['error'] = __('validation.price_in_groups_less_than_cost');
                            return $data;
                        }
                        // // Instantiate the related model class
                        // $relatedModelInstance = new $relatedModel;
                        // $relatedModelInstance->fill($item);
                        // $relatedModelInstance->save();
                    }
                }
            }
        }
        return $data;
    }


    public function update($data_request, $product_id)
    {
        $product = $this->model->find($product_id);
        $product->update($data_request);
        // save categories for product
        $product->categories()->sync($data_request['category_ids']);
        $productTranslation = $this->productTranslationRepository->deleteByProductId($product->id);
        // ProductDiscountCustomerGroup::where('product_id', $product->id)->delete();
        ProductDiscountSellerGroup::where('product_id', $product->id)->delete();
        // ProductPriceCustomerGroup::where('product_id', $product->id)->delete();
        ProductPriceSellerGroup::where('product_id', $product->id)->delete();
        if (!empty($data_request['images'])) {
            ProductImage::where('product_id', $product->id)->delete();
            foreach ($data_request['images'] as $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $image,
                ]);
            }
        }

        // ProductOption::where('product_id',  $product->id)->delete();
        // ProductOptionValue::where('product_id',  $product->id)->delete();
        // Store product options if available
        if (!empty($data_request['product_options'])) {
            // Get all current option IDs for the product options
            $existingOptionIds = $product->product_options()->pluck('option_id')->toArray();

            foreach ($data_request['product_options'] as $option) {
                if (isset($option['option_id'])){
                    // Update or create product option
                    $productOption = ProductOption::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'option_id' => (int) $option['option_id'],
                        ],
                        [
                            'required' => $option['required'] ?? 0,
                        ]
                    );

                    // Delete from $existingOptionIds
                    $existingOptionIds = array_diff($existingOptionIds, [(int) $option['option_id']]);

                    // Create product option values if available
                    if (!empty($option['values'])) {
                        foreach ($option['values'] as $value) {
                            ProductOptionValue::updateOrCreate(
                                [
                                    'product_option_id' => $productOption->id,
                                    'product_id' => $product->id,
                                    'option_id' => $option['option_id'],
                                    'option_value_id' => $value['option_value_id'] ?? null,
                                ],
                                [
                                    'quantity' => $value['quantity'] ?? 0,
                                    'subtract' => $value['subtract'] ?? 1,
                                    'price' => $value['price'] ?? 0.00,
                                    'price_prefix' => $value['price_prefix'] ?? '+',
                                    'points' => $value['points'] ?? 0,
                                    'points_prefix' => $value['points_prefix'] ?? '+',
                                    'weight' => $value['weight'] ?? 0.00000000,
                                    'weight_prefix' => $value['weight_prefix'] ?? '+',
                                ]
                            );
                        }
                    }
                }
            }
            // Delete any product options not present in the request
            if (!empty($existingOptionIds)) {
                ProductOption::where('product_id', $product->id)
                    ->whereIn('option_id', $existingOptionIds)
                    ->delete();
            }
        }
        else{
            ProductOption::where('product_id',  $product->id)->delete();
            ProductOptionValue::where('product_id',  $product->id)->delete();
        }

        if ($productTranslation) {
            $this->productTranslationRepository->store($data_request['name'], $data_request['desc'] ?? null, $data_request['meta_title'] ?? null, $data_request['meta_keyword'] ?? null, $data_request['meta_description'] ?? null, $data_request['content'] ?? null, $data_request['long_desc'] ?? null, $product->id);
            // Now, iterate over each related model and save the data
            $relatedData = [
                // ProductDiscountCustomerGroup::class => [],
                ProductDiscountSellerGroup::class => [],
                // ProductPriceCustomerGroup::class => [],
                ProductPriceSellerGroup::class => []
            ];

            foreach ($relatedData as $relatedModel => $dummyArray) {
                // Assuming your data structure matches your database columns
                $relatedModelData = $data_request[strtolower(class_basename($relatedModel))] ?? [];

                // Save related model data if available

                // Save related model data if available
                if (!empty($relatedModelData)) {
                    foreach ($relatedModelData as $item) {
                        // Instantiate the related model class
                        $relatedModelInstance = new $relatedModel;

                        // Fill the model with data
                        $relatedModelInstance->fill($item);

                        // Save the related model
                        $relatedModelInstance->save();
                    }
                }
            }
        }

        return  $product->load('translations', 'product_options.product_option_value');

    }

    public function changeStatus($data_request, $product_id)
    {
        $product = $this->model->find($product_id);
        $product->update($data_request);
        return $product->load('translations');

    }

    public function show($id)
    {
        return $this->model->where('id', $id)->with(['translations', 'product_images', 'brand', 'categories.ancestors', 'vendor', 'productDiscountSellerGroup', 'productPriceSellerGroup', 'product_options.product_option_value'])->first();
    }

    public function getAllProductsWithRatings($requestData)
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        $validSortColumns = ['last_rating_date', 'name', 'rating_average', 'ratings_count'];
        $sortBy = in_array($requestData->input('sort_by'), $validSortColumns, true) ? $requestData->input('sort_by') : 'last_rating_date';
        $sortDirection = $requestData->input('sort_direction', 'desc');
        $searchTerm = $requestData->input('search', '');
        //$ratingAverage = $requestData->input('rating_average', null);
        $minRatingsCount = $requestData->input('min_ratings_count', null);
        $maxRatingsCount = $requestData->input('max_ratings_count', null);
        $ratingStartDate = $requestData->input('rating_start_date', null);
        $ratingEndDate = $requestData->input('rating_end_date', null);
        $ratingAverage = null;
        if ($requestData->has('rating_average') && $requestData->input('rating_average') != '') {
            $ratingAverage = explode(',', $requestData->input('rating_average', null));
        }
        $categoryIds = null;
        if ($requestData->has('category_ids') && $requestData->input('category_ids') != '') {
            $categoryIds = explode(',', $requestData->input('category_ids', null));
        }
        $brandIds = null;
        if ($requestData->has('brand_ids') && $requestData->input('brand_ids') != '') {
            $brandIds = explode(',', $requestData->input('brand_ids', null));
        }

        $query = Product::query()
        ->leftJoin('product_translations', function (JoinClause $join) use ($langId) {
            $join->on("product_translations.product_id", '=', "products.id")
                ->where("product_translations.language_id", $langId);

        })
        ->leftJoin('ratings', "ratings.product_id", '=', "products.id")
        ->join('brands', function (JoinClause $join) use ($brandIds) {
            $join->on("products.brand_id", '=', "brands.id");
            if (!empty($brandIds)) {
                $join->whereIn('products.brand_id', $brandIds);
            } else {
                $join->orWhereNull('products.brand_id');
            }
        })
        ->select(
            'products.id',
            'products.image',
            DB::raw('AVG(ratings.stars) as rating_average'),
            DB::raw('COUNT(ratings.stars) as ratings_count'),
        )
        ->with([
            'ratings',
        ])
        // ->withCount([
        //     'ratings'
        // ])
        ->groupBy('products.id', 'products.image')
        ->withLastRatingDate();
        $query->orderBy($sortBy, $sortDirection);
        if ($ratingStartDate && $ratingEndDate) {
            $ratingEndDate = Carbon::parse($ratingEndDate);
            $ratingEndDate->addDay();
            $query->havingBetween('last_rating_date', [$ratingStartDate, $ratingEndDate]);
        }
        if ($minRatingsCount && $maxRatingsCount) {
            $query->having('ratings_count', '>=', $minRatingsCount)
                ->having('ratings_count', '<=', $maxRatingsCount);
        }
        if ($ratingAverage) {
            $query->havingRaw('rating_average IN (' . implode(',', $ratingAverage) . ')');
        }
        if ($categoryIds) {
            $query->whereHas('categories', function (Builder $query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            });
        }
        if ($searchTerm) {
            $query->where('product_translations.name', 'like', '%' . $searchTerm . '%');
        }
        return $query->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function showProductWithRatings($id)
    {
        return $this->model
            ->where('id', $id)
            ->select(
                'products.id',
                'products.image',
                'products.brand_id',
            )
            ->with([
                'brand',
                'ratings.customer:id,name,image',
                'ratings.replies.admin:id,name,avatar',
                'ratings.reactions.admin:id,name,avatar',
                'starVotes',
            ])
            ->withCount([
                'ratings'
            ])
            //->withStarVotes()
            ->first();
    }

    public function updateProductQuantity($id, $quantity)
    {
        $product = $this->model->where('id', $id)->first();
        $product->quantity = $product->quantity - $quantity;
        $product->save();
    }

    public function stockAlmostOut($request)
    {
        return $this->model
        ->whereHas('productSerials')
        ->whereColumn('notify', '>=', 'quantity')
        ->select(['id', 'image', 'brand_id', 'quantity', 'notify'])
        ->with(['brand:id'])
        ->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function destroy($id)
    {
        $product = $this->model->where('id', $id)->first();
        if (
            !$product ||
            $product->order_products()->count() > 0 ||
            $product->productSerials()->count() > 0 ||
            $product->vendorProducts()->count() > 0
        ){
            return false;
        }
        //ProductDiscountCustomerGroup::where('product_id', $id)->delete();
        //ProductPriceCustomerGroup::where('product_id', $id)->delete();
        return $product->delete();
    }

    public function multiDelete($requestData)
    {
        foreach ($requestData->ids as $id) {
            $this->destroy($id);
        }
        return true;
    }
    public function makeProductsSaleReport(Request $request)
    {
        $disabledPagination = $request->input('disabled_pagination', 0);
        $langCode = $request->header('lang', 'en');
        // Fetch lang_id from the languages table
        $langId = DB::table('languages')
            ->where('code', $langCode)
            ->value('id');

        $searchTerm = $request->input('search', null); // e.g., "Product A"
        $brandId = $request->input('brand_id', null);       // e.g., 1
        $dateFrom = $request->input('date_from', null);     // e.g., "2024-01-01"
        $dateTo = $request->input('date_to', null);         // e.g., "2024-12-31"

        // Define allowed columns for sorting
        $allowedSortBy = ['sale_date', 'product_name','brand_name','wholesale_price','quantity_sold', 'cost_sold', 'total_sales', 'profit', 'cost_price'];
        // Default to 'created_at' if invalid 'sort_by' input is given
        $sortBy = in_array($request->input('sort_by'), $allowedSortBy)
            ? $request->input('sort_by')
            : 'sale_date';
        // Default to descending order if no 'sort_direction' is provided
        $sortDirection = $request->input('sort_direction', 'asc');
        $query = DB::table('order_products')
            ->join('orders', 'order_products.order_id', '=', 'orders.id')
            ->join('currencies', 'orders.currency_id', '=', 'currencies.id')
            ->join('products', 'order_products.product_id', '=', 'products.id')
            ->leftJoin('product_translations', function ($join) use ($langId) {
                $join->on('products.id', '=', 'product_translations.product_id')
                    ->where('product_translations.language_id', '=', $langId);
            })
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->leftJoin('brand_translations', function ($join) use ($langId) {
                $join->on('brands.id', '=', 'brand_translations.brand_id')
                    ->where('brand_translations.language_id', '=', $langId);
            })
            ->select(
                DB::raw('DATE(order_products.created_at) as sale_date'), // Group by date
                'product_translations.name as product_name',
                'brand_translations.name as brand_name',
                'order_products.coins_number',
                'order_products.cost_price',
                'order_products.unit_price as wholesale_price',
                DB::raw('SUM(order_products.quantity) as quantity_sold'),
                DB::raw('ROUND(SUM((order_products.total_cost / currencies.value)), 3) as cost_sold'),
                DB::raw('ROUND(SUM((order_products.total / currencies.value)), 3) as total_sales'),
                DB::raw('ROUND(SUM((order_products.profit / currencies.value)), 3) as profit'),
            )
            ->where('orders.status', '=', OrderStatus::COMPLETED)
            ->when($searchTerm, function ($query) use($searchTerm) {
                $query->where(function($query) use ($searchTerm) {
                    $query->where('product_translations.name', $searchTerm)
                        ->orWhere('brand_translations.name', $searchTerm);
                });
            })
            ->when($brandId, function ($query, $brandId) {
                $query->where('brands.id', $brandId);
            })
            ->when($dateFrom && $dateTo, function ($query) use ($dateFrom, $dateTo) {
                // add one day for dateTo to use whereBetween
                $dateTo = Carbon::parse($dateTo);
                $dateTo->addDay();
                $query->whereBetween('order_products.created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy(
                'sale_date',
                'products.id',
                'brands.id',
                'product_translations.name',
                'brand_translations.name',
                'order_products.unit_price',
                'order_products.cost_price',
                'order_products.coins_number'
            );

        // Apply sorting
        $query->orderBy($sortBy, $sortDirection);

        // Clone the query for totals calculation
        $totalsQuery = clone $query;

        // Remove grouping and pagination for totals calculation
        $totals = DB::table(DB::raw("({$totalsQuery->toSql()}) as sub"))
            ->mergeBindings($totalsQuery)
            ->select(
                DB::raw('ROUND(SUM(sub.total_sales), 2) as total_sales'),
                DB::raw('ROUND(SUM(sub.cost_sold), 2) as total_cost_sold'),
                DB::raw('ROUND(SUM(sub.profit), 2) as total_profit'),
                DB::raw('COALESCE(SUM(sub.coins_number), 0) as total_coins_number')
            )
            ->first();

        // Paginate the main query
        // $paginatedData = $query->paginate(PAGINATION_COUNT_ADMIN);
        if ($disabledPagination == 1) {
            $reportData = $query->get();
        } else {
            $reportData = $query->paginate(PAGINATION_COUNT_ADMIN);
        }
        // Return the result
        return [
            'data' => $reportData,
            'totals' => [
                'total_sales' => $totals->total_sales ?? 0,
                'total_cost_sold' => $totals->total_cost_sold ?? 0,
                'total_profit' => $totals->total_profit ?? 0,
                'total_coins_number' => $totals->total_coins_number ?? 0,
            ],
        ];
    }

    /**
     * Product Model
     *
     * @return string
     */
    public function model(): string
    {
        return Product::class;
    }
}
