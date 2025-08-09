<?php

namespace App\Repositories\Admin;

use App\Models\SellerGroupLevel;
use App\Models\Language;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupLevelRepository extends BaseRepository
{

    private $sellerGroupLevelTranslationRepository;
    private $languageRepository;

    public function __construct(Application $app, SellerGroupLevelTranslationRepository $sellerGroupLevelTranslationRepository, LanguageRepository $languageRepository)
    {
        parent::__construct($app);

        $this->sellerGroupLevelTranslationRepository = $sellerGroupLevelTranslationRepository;
        $this->languageRepository = $languageRepository;

    }

    public function getAllsellerGroupLevels($requestData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if(in_array($requestData->input('sort_by'), ['created_at', 'name', 'sellers_count', 'status']))
            $sortBy = $requestData->input('sort_by');
        else
            $sortBy = 'created_at';
        $sortDirection = $requestData->input('sort_direction', 'desc');
        $local = app()->getLocale();
        $langId = $this->languageRepository->getLangByCode($local)->id;
        $searchTerm = $requestData->input('search', '');
        $query = $this->model->query();
        $query->leftJoin('sellers', "sellers.seller_group_level_id", '=', "seller_group_levels.id");
        $query->leftJoin('seller_group_level_translations', function (JoinClause $join) use ($langId) {
            $join->on("seller_group_level_translations.seller_group_level_id", '=', "seller_group_levels.id")
                ->where("seller_group_level_translations.language_id", $langId);
        });
        $query->select('seller_group_levels.*', DB::raw('count(sellers.id) as sellers_count'));
        $query->groupBy([
            "id",
            "parent_id",
            "image",
            "status",
            "sort_order",
            "deleted_at",
            "created_at",
            "updated_at",
        ]);
        $query->with(['translations']);
        // Apply sorting
        if ($sortBy == 'name')
            $query->orderBy('seller_group_level_translations.name', $sortDirection);
        elseif ($sortBy == 'sellers_count')
            $query->orderBy('sellers_count', $sortDirection);
        else
            $query->orderBy($sortBy, $sortDirection);
        // Apply search
        if ($searchTerm) {
            $query->where('seller_group_level_translations.name', 'like', '%' . $searchTerm . '%');
        }
        return $query->latest()->paginate(PAGINATION_COUNT_ADMIN);
    }

    public function trash()
    {
        return $this->model->onlyTrashed()->get();
    }

    public function getAllCategoriesWithChild()
    {
        return $this->model->with('child')->whereNull('parent_id')->get();
    }


    public function store($data_request)
    {
        $sellerGroupLevel = $this->model->create($data_request);
        if ($sellerGroupLevel)
            $this->sellerGroupLevelTranslationRepository->store($data_request['name'],$data_request['desc'], $sellerGroupLevel->id);

        return $sellerGroupLevel;

    }

    public function update($data_request, $sellerGroupLevel_id)
    {
        $sellerGroupLevel = $this->model->find($sellerGroupLevel_id);
        $sellerGroupLevel->update($data_request);
        $sellerGroupLevelTranslation = $this->sellerGroupLevelTranslationRepository->deleteBySellerGroupLevelId($sellerGroupLevel->id);
        if ($sellerGroupLevelTranslation)
            $this->sellerGroupLevelTranslationRepository->store($data_request['name'],$data_request['desc'], $sellerGroupLevel->id);

        return $sellerGroupLevel;

    }

    public function show($id)
    {
        return $this->model->where('id', $id)->with('translations')->first();
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)->restore();

    }

    public function update_status($data_request, $sellerGroupLevel_id)
    {
        $sellerGroupLevel = $this->model->find($sellerGroupLevel_id);
        $sellerGroupLevel->update($data_request);
        return $sellerGroupLevel;

    }

    /**
     * SellerGroupLevel Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\SellerGroupLevel";
    }
}
