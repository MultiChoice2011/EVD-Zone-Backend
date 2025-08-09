<?php

namespace App\Repositories\Admin;

use App\Models\SellerAddress;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerAddressRepository extends BaseRepository
{

    public function store($requestData, $sellerId): bool
    {
        $this->model->updateOrCreate([
            'seller_id' => $sellerId,
        ],[
            'country_id' => $requestData->country_id,
            'city_id' => $requestData->city_id ?? null,
            'region_id' => $requestData->region_id ?? null,
            'street' => $requestData->street ?? null,
        ]);
        return true;
    }

    public function deleteBySellerId($sellerId)
    {
        return $this->model->where('seller_id',$sellerId)->delete();
    }


    public function model(): string
    {
        return SellerAddress::class;
    }
}
