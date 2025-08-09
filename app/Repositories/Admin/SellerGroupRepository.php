<?php

namespace App\Repositories\Admin;

use App\Models\SellerGroup;
use App\Models\Language;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupRepository extends BaseRepository
{

    private $sellerGroupTranslationRepository;
    private $sellerGroupCustomPriceRepository;
    private $sellerGroupCustomProductPriceRepository;
    private $sellerGroupConditionRepository;
    private $languageRepository;

    public function __construct(Application $app, SellerGroupTranslationRepository $sellerGroupTranslationRepository, SellerGroupCustomPriceRepository $sellerGroupCustomPriceRepository, SellerGroupCustomProductPriceRepository $sellerGroupCustomProductPriceRepository, SellerGroupConditionRepository $sellerGroupConditionRepository, LanguageRepository $languageRepository)
    {
        parent::__construct($app);

        $this->sellerGroupTranslationRepository = $sellerGroupTranslationRepository;
        $this->sellerGroupCustomPriceRepository = $sellerGroupCustomPriceRepository;
        $this->sellerGroupCustomProductPriceRepository = $sellerGroupCustomProductPriceRepository;
        $this->sellerGroupConditionRepository = $sellerGroupConditionRepository;
        $this->languageRepository = $languageRepository;

    }

    public function getAllsellerGroups($requestData): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (in_array($requestData->input('sort_by'), ['created_at', 'name', 'sellers_count', 'status', 'automatic']))
            $sortBy = $requestData->input('sort_by');
        else
            $sortBy = 'created_at';
        $sortDirection = $requestData->input('sort_direction', 'desc');
        $local = app()->getLocale();
        $langId = $this->languageRepository->getLangByCode($local)->id;
        $searchTerm = $requestData->input('search', '');
        $query = $this->model->query();
        $query->leftJoin('sellers', "sellers.seller_group_id", '=', "seller_groups.id");
        $query->leftJoin('seller_group_translations', function (JoinClause $join) use ($langId) {
            $join->on("seller_group_translations.seller_group_id", '=', "seller_groups.id")
                ->where("seller_group_translations.language_id", $langId);
        });
        $query->select('seller_groups.*', DB::raw('count(sellers.id) as sellers_count'));
        $query->groupBy([
            "id",
            "parent_id",
            "image",
            "automatic",
            "amount_sales",
            "order_count",
            "status",
            "sort_order",
            "conditions_type",
            "deleted_at",
            "created_at",
            "updated_at",
            "auto_assign",
        ]);
        $query->with(['translations', 'seller_group_custom_product_prices', 'seller_group_custom_prices', 'conditions']);
        // Apply sorting
        if ($sortBy == 'name')
            $query->orderBy('seller_group_translations.name', $sortDirection);
        elseif ($sortBy == 'sellers_count')
            $query->orderBy('sellers_count', $sortDirection);
        else
            $query->orderBy($sortBy, $sortDirection);
        // Apply search
        if ($searchTerm) {
            $query->where('seller_group_translations.name', 'like', '%' . $searchTerm . '%');
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
        $sellerGroup = $this->model->create($data_request);
        if ($sellerGroup) {
            $this->sellerGroupTranslationRepository->store($data_request, $sellerGroup->id);
            if (isset($data_request['customProductPrice']))
                $this->sellerGroupCustomProductPriceRepository->store($data_request['customProductPrice'], $sellerGroup->id);
            if (isset($data_request['customPrice']))
                $this->sellerGroupCustomPriceRepository->store($data_request['customPrice'], $sellerGroup->id);

            $this->sellerGroupConditionRepository->store($data_request, $sellerGroup->id);
        }

        return $sellerGroup->load('translations', 'conditions');


    }

    public function update($data_request, $sellerGroup_id)
    {
        $sellerGroup = $this->model->find($sellerGroup_id);
        $sellerGroup->update($data_request);
        $sellerGroupTranslation = $this->sellerGroupTranslationRepository->deleteBySellerGroupId($sellerGroup->id);
        $sellerGroupCustomProductPrice = $this->sellerGroupCustomProductPriceRepository->deleteBySellerGroupId($sellerGroup->id);
        $sellerGroupCustomPrice = $this->sellerGroupCustomPriceRepository->deleteBySellerGroupId($sellerGroup->id);
        // delete old values for conditions
        $customerGroupCondition = $this->sellerGroupConditionRepository->deleteBySellerGroupId($sellerGroup->id);
        if ($sellerGroupTranslation) {
            $this->sellerGroupTranslationRepository->store($data_request, $sellerGroup->id);
            if (isset($data_request['customProductPrice']))
                $this->sellerGroupCustomProductPriceRepository->store($data_request['customProductPrice'], $sellerGroup->id);
            if (isset($data_request['customPrice']))
                $this->sellerGroupCustomPriceRepository->store($data_request['customPrice'], $sellerGroup->id);
            $this->sellerGroupConditionRepository->store($data_request, $sellerGroup->id);

        }

        return $sellerGroup->load('translations', 'conditions');

    }

    public function show($id)
    {
        return $this->model->where('id', $id)->with(['translations', 'seller_group_custom_product_prices', 'seller_group_custom_prices', 'conditions'])->first();
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)->restore();

    }

    public function update_status($data_request, $sellerGroup_id)
    {
        $sellerGroup = $this->model->find($sellerGroup_id);
        $sellerGroup->update($data_request);
        return $sellerGroup;

    }

    public function auto_assign($data_request, $sellerGroup_id)
    {
        $sellerGroup = $this->model->find($sellerGroup_id);
        $sellerGroup->update($data_request);
        return $sellerGroup;

    }
    public function destroy_selected($ids)
    {
        foreach ($ids as $id) {
            $this->destroy($id);
        }
        return true;
    }

    /**
     * SellerGroup Model
     *
     * @return string
     */
    public function model(): string
    {
        return SellerGroup::class;
    }
}
