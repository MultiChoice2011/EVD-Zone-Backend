<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class AttributeGroupTranslationRepository extends BaseRepository
{

    public function store($data_request, $attribute_group_id)
    {
        foreach ($data_request as $language_id => $value) {
             $this->model->create(
                [
                    'attribute_group_id' => $attribute_group_id,
                    'language_id' => $language_id ,
                    'name' => $value,
                ]);
        }
        return true;
    }

    public function deleteByAttributeGroupId($attribute_group_id)
    {
        return $this->model->where('attribute_group_id',$attribute_group_id)->delete();
    }
    /**
     * AttributeGroup Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\AttributeGroupTranslation";
    }
}
