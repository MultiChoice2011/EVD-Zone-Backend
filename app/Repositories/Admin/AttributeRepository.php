<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use App\Models\ProductAttribute;
use Illuminate\Container\Container as Application;
use Prettus\Repository\Eloquent\BaseRepository;
use  App\Repositories\Admin\AttributeTranslationRepository;
class AttributeRepository extends BaseRepository
{

    private $attributeTranslationRepository;

    public function __construct(Application $app, AttributeTranslationRepository $attributeTranslationRepository)
    {
        parent::__construct($app);

        $this->attributeTranslationRepository = $attributeTranslationRepository;

    }

    public function getAllAttributes(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->with('translations')->get();
    }
    public function getAllAttributesForm(): \Illuminate\Database\Eloquent\Collection
    {
        $lang = Language::where('code', app()->getLocale())->first();
        return $this->model->leftJoin('attribute_translations', function ($join) {
            $join->on('attributes.id', '=', 'attribute_translations.attribute_id');
        })->select('attributes.*')->addSelect('attribute_translations.name')
            ->where('attribute_translations.language_id', $lang->id)
            ->get();
    }
    public function getProductAttributes($product_id)
    {
        $product_attribute_data = array();
        $product_attribute_query = ProductAttribute::where('product_id',$product_id)->select("attribute_id")->groupBy('attribute_id')->get();
        foreach ($product_attribute_query as $product_attribute) {
            $product_attribute_description_data = array();
            $product_attribute_description_query = ProductAttribute::where('product_id',$product_id)->where('attribute_id',$product_attribute['attribute_id'])->get();
            foreach ($product_attribute_description_query as $product_attribute_description) {
                $product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
            }
            $product_attribute_data[] = array(
                'name'                          => $this->attributeTranslationRepository->getAttributeNameByAttributeID($product_attribute['attribute_id']),
                'attribute_id'                  => $product_attribute['attribute_id'],
                'product_attribute_description' => $product_attribute_description_data
            );
        }
        return $product_attribute_data;
    }
    public function store($data_request)
    {
        $attribute = $this->model->create($data_request);
        if ($attribute)
            $this->attributeTranslationRepository->store($data_request['name'], $attribute->id);

        return $attribute;

    }

    public function update($data_request,$attribute_id)
    {
        $attribute = $this->model->find($attribute_id);
        $attribute->update($data_request);
        $attributeTranslation = $this->attributeTranslationRepository->deleteByAttributeId($attribute->id);
        if ($attributeTranslation)
            $this->attributeTranslationRepository->store($data_request['name'], $attribute->id);

        return $attribute;

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
     * Attribute Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\Attribute";
    }
}
