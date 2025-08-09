<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupCustomProductPriceRepository extends BaseRepository
{

    public function store($data_request, $sellerGroup_id)
    {
        return $this->model->create(
            [
                'seller_group_id' => $sellerGroup_id,
                'advantage' => $data_request['advantage'],
                'type' => $data_request['type'],
                'amount' => $data_request['amount'],
            ]);
    }

    public function deleteBySellerGroupId($sellerGroup_id)
    {
        return $this->model->where('seller_group_id', $sellerGroup_id)->delete();
    }

    /**
     * SellerGroup Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\SellerGroupCustomProductPrice";
    }
}
