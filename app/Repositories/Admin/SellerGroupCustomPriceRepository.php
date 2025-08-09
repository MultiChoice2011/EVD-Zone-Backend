<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupCustomPriceRepository extends BaseRepository
{

    public function store($data_request, $sellerGroup_id)
    {
        foreach ($data_request as  $value) {
             $this->model->create(
                [
                    'seller_group_id' => $sellerGroup_id,
                    'model_id' => $value['model_id'],
                    'advantage' => $value['advantage'],
                    'type' => $value['type'],
                    'amount' => $value['amount'],
                ]);
        }
        return true;
    }

    public function deleteBySellerGroupId($sellerGroup_id)
    {
        return $this->model->where('seller_group_id',$sellerGroup_id)->delete();
    }
    /**
     * SellerGroup Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\SellerGroupCustomPrice";
    }
}
