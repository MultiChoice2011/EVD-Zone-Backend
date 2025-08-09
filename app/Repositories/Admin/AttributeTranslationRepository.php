<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class AttributeTranslationRepository extends BaseRepository
{

    public function store($data_request, $attribute_id)
    {
        foreach ($data_request as $language_id => $value) {
             $this->model->create(
                [
                    'attribute_id' => $attribute_id,
                    'language_id' => $language_id ,
                    'name' => $value,
                ]);
        }
        return true;
    }
    public function getAttributeNameByAttributeID($attribute_id)
    {
        $lang = Language::where('code', app()->getLocale())->first();
        $attribute_name =  $this->model->where('attribute_id', $attribute_id)->where('language_id', $lang->id)->first();
       return $attribute_name['name'];
    }
    public function deleteByAttributeId($attribute_id)
    {
        return $this->model->where('attribute_id',$attribute_id)->delete();
    }
    /**
     * Attribute Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\AttributeTranslation";
    }
}
