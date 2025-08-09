<?php
namespace App\Repositories\Seller;

use App\Enums\GeneralStatusEnum;
use App\Models\Category;
use App\Repositories\Admin\LanguageRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Container\Container as Application;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Eloquent\BaseRepository;

class CategoryRepository extends BaseRepository
{
    public function __construct(
        Application                                     $app,
        private LanguageRepository                      $languageRepository
    ){
        parent::__construct($app);
    }

    public function getAllCategories()
    {
        $categories = $this->getModel()::get(['id','name']);
        return $categories;
    }
    public function getMainCategories()
    {
        return $this->getModel()::with(['brand','parent'])
            ->withCount('child')
            ->whereNull('parent_id')
            ->active()
            ->get();
    }

    public function getSubCategories($requestData)
    {
        $parentId = $requestData->input('parent_id', null);
        $isBrands = $requestData->input('is_brands', 0);
        $searchTerm = $requestData->input('name', null);
        $perPage = $requestData->input('per_page', null);

        $langId = $this->languageRepository->getLangByCode(app()->getLocale())->id;
        $subcategoriesQuery = $this->getModel()::query();
        $subcategoriesQuery->leftJoin('brands', "categories.brand_id", '=', "brands.id")
            ->where(function ($query) {
                $query->where('brands.status', '=', GeneralStatusEnum::getStatusActive())
                    ->orWhereNull('brands.id');
            });
        $subcategoriesQuery->leftJoin('brand_translations', function ($join) use ($langId) {
            $join->on("brand_translations.brand_id", '=', "brands.id")->where("brand_translations.language_id", $langId);
        });

        if (! $parentId) {
            $subcategoriesQuery->whereNotNull('categories.brand_id');
        }
        elseif ($parentId && $isBrands) {
            $allSubcategoryIds = $this->getAllSubcategoryIds($parentId);
            $subcategoriesQuery->whereIn('categories.id', $allSubcategoryIds)
                ->whereNotNull('categories.brand_id');
        }
        else{
            $subcategoriesQuery->where('parent_id', $parentId);
        }

        $subcategoriesQuery->with(['brand','parent'])
            ->withCount('child')
            ->active();
        if ($searchTerm){
            $subcategoriesQuery->where('brand_translations.name','LIKE', "%{$searchTerm}%");
        }

        $subcategories = $subcategoriesQuery->paginate($perPage ?? PAGINATION_COUNT_SELLER);

        return $subcategories;
    }

    private function getAllSubcategoryIds($categoryId)
    {
        $category = $this->getModel()::find($categoryId);
        if (!$category) {
            return [];
        }

        $subcategories = $category->child;
        $allSubcategoryIds = $subcategories->pluck('id')->toArray();

        foreach ($subcategories as $subcategory) {
            $allSubcategoryIds = array_merge(
                $allSubcategoryIds,
                $this->getAllSubcategoryIds($subcategory->id)
            );
        }

        return $allSubcategoryIds;
    }

    public function show($id)
    {
        return $this->getModel()::where('id', $id)
            ->active()
            ->first();
    }
    public function getMainCategoriesIds()
    {
        return $this->getModel()::whereNull('parent_id')->pluck('id')->toArray();
    }
    public function getSubCategoriesIds($parentId)
    {
        return $this->getModel()::where('parent_id', $parentId)->pluck('id')->toArray();
    }
    private function getModelById($id)
    {
        return $this->getModel()::find($id);
    }
    public function model()
    {
        return Category::class;
    }
}
