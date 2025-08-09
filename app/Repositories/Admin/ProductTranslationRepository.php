<?php

namespace App\Repositories\Admin;

use App\Models\Language;
use Illuminate\Support\Str;
use Prettus\Repository\Eloquent\BaseRepository;

class ProductTranslationRepository extends BaseRepository
{

    public function store($data_request, $data_request_desc, $data_request_meta_title, $data_request_meta_keyword, $data_request_meta_description, $data_request_content, $data_request_long_desc, $product_id)
    {
        foreach ($data_request as $language_id => $value) {
            $product_translation  = $this->model->create(
                [
                    'product_id' => $product_id,
                    'language_id' => $language_id,
                    'name' => $value,
                ]);
            $product_translation->desc =  $data_request_desc[$language_id] ?? null;
            $product_translation->meta_title =  $data_request_meta_title[$language_id] ?? null;
            $product_translation->meta_keyword =  $data_request_meta_keyword[$language_id] ?? null;
            $product_translation->meta_description =  $data_request_meta_description[$language_id] ?? null;
            $product_translation->content =  $data_request_content[$language_id] ?? null;
            $product_translation->save();
        }
        return true;
    }

    public function deleteByProductId($product_id)
    {
        return $this->model->where('product_id', $product_id)->delete();
    }

    /**
     * Product Model
     *
     * @return string
     */
    public function model(): string
    {
        return "App\Models\ProductTranslation";
    }
}
