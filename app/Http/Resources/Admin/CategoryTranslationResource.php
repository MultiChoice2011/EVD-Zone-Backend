<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTranslationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "category_id" => $this->category_id,
            "language_id" => $this->language_id,
            "name" => $this->name,
            "description" => $this->description,
            "meta_title" => $this->meta_title,
            "meta_keyword" => $this->meta_keyword,
            "meta_description" => $this->meta_description,
        ];
    }
}
