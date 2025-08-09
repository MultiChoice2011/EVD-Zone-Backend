<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class OptionTranslationRepository extends BaseRepository
{

    public function store($data_request, $option_id)
    {
        foreach ($data_request as $language_id => $value) {
             $this->model->create(
                [
                    'option_id' => $option_id,
                    'language_id' => $language_id ,
                    'name' => $value,
                ]);
        }
        return true;
    }

    public function deleteByOptionId($option_id)
    {
        return $this->model->where('option_id',$option_id)->delete();
    }
    /**
     * Option Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\OptionTranslation";
    }
}
