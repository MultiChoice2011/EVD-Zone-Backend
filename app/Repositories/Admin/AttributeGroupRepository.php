<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use  App\Repositories\Admin\AttributeGroupTranslationRepository;
class AttributeGroupRepository extends BaseRepository
{

    private $attributeGroupTranslationRepository;

    public function __construct(Application $app, AttributeGroupTranslationRepository $attributeGroupTranslationRepository)
    {
        parent::__construct($app);

        $this->attributeGroupTranslationRepository = $attributeGroupTranslationRepository;

    }

    public function getAllAttributeGroupsForm()
    {
        $lang = Language::where('code', app()->getLocale())->first();
        return $this->model->leftJoin('attribute_group_translations', function ($join) {
            $join->on('attribute_groups.id', '=', 'attribute_group_translations.attribute_group_id');
        })->select('attribute_groups.*')->addSelect('attribute_group_translations.name')
            ->where('attribute_group_translations.language_id', $lang->id)
            ->get();
    }
    public function getAllAttributeGroups(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('translations')->get();
    }

    public function store($data_request)
    {
        $attributeGroup = $this->model->create($data_request);
        if ($attributeGroup)
            $this->attributeGroupTranslationRepository->store($data_request['name'], $attributeGroup->id);

        return $attributeGroup;

    }

    public function update($data_request,$attributeGroup_id)
    {
        $attributeGroup = $this->model->find($attributeGroup_id);
        $attributeGroup->update($data_request);
        $attributeGroupTranslation = $this->attributeGroupTranslationRepository->deleteByAttributeGroupId($attributeGroup->id);
        if ($attributeGroupTranslation)
            $this->attributeGroupTranslationRepository->store($data_request['name'], $attributeGroup->id);

        return $attributeGroup;

    }

    public function show($id)
    {
        return $this->model->where('id',$id)->with('translations')->first();
    }
    public function destroy($id)
    {
        return $this->model->where('id',$id)->delete();
    }

    /**
     * AttributeGroup Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\AttributeGroup";
    }

}
