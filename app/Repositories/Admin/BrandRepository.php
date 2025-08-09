<?php

namespace App\Repositories\Admin;

use App\Models\Category;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;
class BrandRepository extends BaseRepository
{

    private $brandTranslationRepository;
    private $languageRepository;
    private $brandImageRepository;
    private $categoryRepository;

    public function __construct(
        Application $app ,
        BrandTranslationRepository $brandTranslationRepository,
        LanguageRepository $languageRepository,
        BrandImageRepository $brandImageRepository,
        CategoryRepository $categoryRepository
    )
    {
        parent::__construct($app);
        $this->brandTranslationRepository = $brandTranslationRepository;
        $this->brandImageRepository = $brandImageRepository;
        $this->languageRepository = $languageRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllBrands($requestData)
    {
        if (in_array($requestData->input('sort_by'), ['created_at', 'name', 'status']))
            $sortBy = $requestData->input('sort_by');
        else
            $sortBy = 'created_at';
        $sortDirection = $requestData->input('sort_direction', 'desc');
        $searchTerm = $requestData->input('search', '');
        $perPage = $requestData->input('per_page', PAGINATION_COUNT_ADMIN);
        $categoriesFilter = null;
        if ($requestData->has('categories_filter') && $requestData->input('categories_filter') != '') {
            $categoriesFilter = explode(',', $requestData->input('categories_filter', null));
        }
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;

        $query = $this->model->query();
        $query->leftJoin('brand_translations', function (JoinClause $join) use ($langId) {
            $join->on("brand_translations.brand_id", '=', "brands.id")
                ->where("brand_translations.language_id", $langId);
        });
        $query->leftJoin('category_brands', "category_brands.brand_id", '=', "brands.id");
        if (!empty($categoriesFilter)) {
            $allBrandIds = $this->categoryRepository->getBrandIdsForCategoryIdsAncestors($categoriesFilter);
            $query->whereIn('brands.id', $allBrandIds);
        }
        $query->select('brands.*');
        $query->groupBy([
            "id",
            "status",
            "created_at",
            "updated_at",
        ]);
        $query->orderBy($sortBy == 'name' ? 'brand_translations.'.$sortBy : $sortBy, $sortDirection);
        // Apply searching
        if ($searchTerm) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        return $query->with(['translations','images'])->paginate($perPage);
        //return $this->model->with('translations')->paginate(10);

    }

    public function getHomeSectionsBrands($requestData)
    {
        $categoriesFilter = null;
        if ($requestData->has('categories_filter') && $requestData->input('categories_filter') != '') {
            $categoriesFilter = explode(',', $requestData->input('categories_filter', null));
        }
        if($categoriesFilter){
            $subcategoryIds = $categoriesFilter && count($categoriesFilter) > 0 ? Category::whereIn('parent_id', $categoriesFilter)->pluck('id')->toArray() : null;
        }
        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;

        $query = $this->model->query();
        $query->leftJoin('brand_translations', function (JoinClause $join) use ($langId) {
            $join->on("brand_translations.brand_id", '=', "brands.id")
                ->where("brand_translations.language_id", $langId);
        });
        $query->leftJoin('category_brands', "category_brands.brand_id", '=', "brands.id");
        if ($subcategoryIds) {
            $query->whereIn("category_brands.category_id", $subcategoryIds);
        }
        $query->select('brands.*');
        $query->groupBy([
            "id",
            "status",
            "created_at",
            "updated_at",
        ]);
        return $query->with(['translations'])->paginate(PAGINATION_COUNT_ADMIN);

    }


    public function store($requestData)
    {
        $brand =  $this->model->create($requestData->all());
        if ($brand){
            $this->brandTranslationRepository->storeOrUpdate($requestData, $brand->id);
            $this->brandImageRepository->storeOrUpdate($requestData, $brand->id);
        }

        return $brand->load('translations','images');
    }

    public function show($brand_id)
    {
        return $this->model->with(['translations','images'])->find($brand_id);

    }

    public function updateBrand($requestData, $brand_id)
    {
        $brand = $this->model->find($brand_id);
        $brand->update($requestData->all());
        $this->brandTranslationRepository->storeOrUpdate($requestData, $brand->id);
        $this->brandImageRepository->storeOrUpdate($requestData, $brand->id);
        return $brand->load('translations','images');
    }

    public function changeStatus($requestData, $id)
    {
        $brand = $this->model->where('id', $id)->first();
        if(!$brand)
            return false;
        $brand->status = $requestData->status;
        $brand->save();
        return $brand;
    }

    public function destroy($id)
    {
        $brand = $this->model->where('id', $id)->first();
        if (
            $brand->order_products()->count() > 0 ||
            $brand->category_brands()->count() > 0 ||
            $brand->products()->count() > 0
        ){
            return false;
        }
        return $brand->delete();
    }

    public function destroy_selected($ids)
    {
        foreach ($ids as $id) {
            $this->destroy($id);
        }
        return true;
    }

    /**
     * Brand Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Brand";
    }
}
