<?php

namespace App\Repositories\Admin;

use App\Enums\SellerGroupConditionType;
use App\Models\SellerGroupCondition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupConditionRepository extends BaseRepository
{

    public function store($requestData, $sellerGroupId): bool
    {
        if (!isset($requestData['conditions']) || count($requestData['conditions']) < 1)
            return true;
        foreach ($requestData['conditions'] as $type => $condition) {
            $this->model->create([
                'seller_group_id' => $sellerGroupId,
                'condition_type' => $type,
                'comparison_operator' => $type == SellerGroupConditionType::getTypeRegionId() ? null : $condition['operator'],
                'value' => $type == SellerGroupConditionType::getTypeRegionId() ? json_encode($condition['value']) : $condition['value'],
                'value2' =>
                    (   $type != SellerGroupConditionType::getTypeRegionId() &&
                        $condition['operator'] == 'between'
                    )? $condition['value2'] : null,
            ]);
        }
        return true;
    }

    public function deleteBySellerGroupId($sellerGroupId)
    {
        return $this->model->where('seller_group_id',$sellerGroupId)->delete();
    }


    /**
     * SellerGroupCondition Model
     *
     * @return string
     */
    public function model(): string
    {
        return SellerGroupCondition::class;
    }
}
