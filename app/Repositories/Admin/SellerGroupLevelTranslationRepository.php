<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class SellerGroupLevelTranslationRepository extends BaseRepository
{

    public function store($data_request,$description, $sellerGroupLevel_id)
    {
        foreach ($data_request as $language_id => $value) {
            $translation = $this->model->create(
                [
                    'seller_group_level_id' => $sellerGroupLevel_id,
                    'language_id' =>$language_id ,
                    'name' => $value,
                ]);
            $translation->desc =  $description[$language_id];

            $translation->save();

        }
        return true;
    }

    public function deleteBySellerGroupLevelId($sellerGroupLevel_id)
    {
        return $this->model->where('seller_group_level_id',$sellerGroupLevel_id)->delete();
    }
    /**
     * SellerGroupLevel Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\SellerGroupLevelTranslation";
    }
}
