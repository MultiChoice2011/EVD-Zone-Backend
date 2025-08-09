<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class CountryTranslationRepository extends BaseRepository
{

    public function store($data_request, $country_id)
    {
        foreach ($data_request as $language_id => $value) {
             $this->model->create(
                [
                    'country_id' => $country_id,
                    'language_id' =>$language_id ,
                    'name' => $value,
                ]);
        }
        return true;
    }

    public function deleteByCountryId($country_id)
    {
        return $this->model->where('country_id',$country_id)->delete();
    }
    /**
     * Country Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\CountryTranslation";
    }
}
