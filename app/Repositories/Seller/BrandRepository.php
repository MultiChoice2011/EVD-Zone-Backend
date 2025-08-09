<?php
namespace App\Repositories\Seller;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Repositories\Admin\LanguageRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Type\Integer;

class BrandRepository extends BaseRepository
{
    public function __construct(
        Application $app,
        private LanguageRepository      $languageRepository,
        private CategoryRepository      $categoryRepository,
    )
    {
        parent::__construct($app);
    }
    public function getBrandsWithoutPaginate()
    {
        return $this->model->orderByDesc('id')->get();
    }
    public function getAllBrands($request)
    {
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        $searchTerm = $request->input('name'); // Search by brand name
        $categoryFilter = $request->input('category_filter', null);
        $category = $this->categoryRepository->show($categoryFilter);

        $brands = $this->model->query();

        if ($category) {
            $brands = $this->brandsFilteredQuery($brands, $langId, $category);
        }else{
            $brands = $this->allBrandsQuery($brands, $langId);
        }

        $brands->when($searchTerm, function ($query, $searchTerm) {
                $query->whereHas('translations', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%");
                });
            })
            ->with(['images'])
            ->groupBy([
                'id',
                'status',
                'created_at',
                'updated_at',
                'category_id',
                'category_name',
            ]);

        // Paginate the results
        $brands = $brands->paginate(PAGINATION_COUNT_SELLER);

        $brands = $this->formatBrandsWithHasSubs($brands, $category);

        return $brands;
    }
    private function allBrandsQuery(Builder $query, int $langId): Builder
    {
        $query->select('brands.*', DB::raw('COALESCE(parent_categories.id, child_categories.id) as category_id'),
            DB::raw('COALESCE(parent_category_translations.name, child_category_translations.name) as category_name'))
            ->join('category_brands', 'brands.id', '=', 'category_brands.brand_id')
            ->join('categories as child_categories', 'category_brands.category_id', '=', 'child_categories.id')
            ->leftJoin('categories as parent_categories', 'child_categories.parent_id', '=', 'parent_categories.id')
            ->leftJoin('category_translations as child_category_translations', function (JoinClause $join) use ($langId) {
                $join->on("child_category_translations.category_id", '=', "child_categories.id")
                    ->where("child_category_translations.language_id", $langId);
            })
            ->leftJoin('category_translations as parent_category_translations', function (JoinClause $join) use ($langId) {
                $join->on("parent_category_translations.category_id", '=', "parent_categories.id")
                    ->where("parent_category_translations.language_id", $langId);
            })
            ->whereNull('child_categories.deleted_at')
            ->whereNull('parent_categories.deleted_at');

        return $query;
    }

    private function brandsFilteredQuery(Builder $query, int $langId, Category $category): Builder
    {
        $subcategoryIds = [];
        $subcategoryIds = $this->categoryRepository->getSubCategoriesIds($category->id);
        $subcategoryIds[] = $category->id;
        $query->select('brands.*')
            ->selectRaw('? as category_id', [$category->id])
            ->selectRaw('? as category_name', [$category->name])
            ->leftJoin('category_brands', 'brands.id', '=', 'category_brands.brand_id')
            ->join('categories', 'category_brands.category_id', '=', 'categories.id')
            ->leftJoin('category_translations', function (JoinClause $join) use ($langId) {
                $join->on("category_translations.category_id", '=', "categories.id")
                    ->where("category_translations.language_id", $langId);
            })
            ->whereIn("category_brands.category_id", $subcategoryIds)
            ->whereNull('categories.deleted_at');
        return $query;
    }

    public function getBrandDetails($id)
    {
        $brand = $this->model
            ->select('id')
            ->where('id', $id)
            ->active()
            ->first();
        if ($brand){
            $brand->images = $brand->getImagesArray();
        }
        return $brand;
    }


    public function getProductIds($categoryId)
    {
        // Fetch product IDs from the pivot table
        $productIds = ProductCategory::where('category_id', $categoryId)->pluck('product_id');
        return $productIds;
    }
    public function getProducts($productIds)
    {
        $searchTerm = request()->input('name'); // Search by brand name
        // Fetch products using the retrieved IDs
        $products = Product::whereIn('id', $productIds)
        ->when($searchTerm, function ($query, $searchTerm) {
            $query->whereHas('translations', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%");
            });
        })
        ->get();
        return $products;
    }
    private function formatBrandsWithHasSubs(LengthAwarePaginator $brands, Category $category=null)
    {
        $brands->map(function ($item) use($category) {
            if ($item->images->isEmpty()) {
                $item->setRelation('images', collect([
                    (object)[
                        'brand_id' => $item->id,
                        'key' => 'logo',
                        'image' => config('services.cloudinary.default_image')
                    ]
                ]));
            }
            $item->has_subs = 0;
            if($item->category_brands()->where('category_id', $category ? $category->id : $item->category_id)->first())
                $item->has_subs = 0;
            elseif($item->categories()->count() > 0)
                $item->has_subs = 1;
            return $item;
        });

        return $brands;
    }

    public function model()
    {
        return Brand::class;
    }
}
