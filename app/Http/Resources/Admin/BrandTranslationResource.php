<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandTranslationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "brand_id" => $this->brand_id,
            "language_id" => $this->language_id,
            "name" => $this->name,
            "description" => $this->description,
        ];
    }
}
