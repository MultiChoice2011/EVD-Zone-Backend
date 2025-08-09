<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "status" => $this->status,
            "translations" => $this->when( $this->translations, BrandTranslationResource::collection($this->whenLoaded('translations')) ),
            "images" => $this->when( $this->images, BrandImageResource::collection($this->whenLoaded('images')) ),
        ];
    }
}
