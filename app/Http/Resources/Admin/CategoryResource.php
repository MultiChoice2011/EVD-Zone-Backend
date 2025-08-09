<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class CategoryResource extends JsonResource
{
    /**
     * Category data into an array.
     *
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->image,
            "parent_id" => $this->parent_id,
            "brand_id" => $this->brand_id,
            "level" => $this->level,
            "status" => $this->status,
            "is_topup" => $this->is_topup,
            "web" => $this->web,
            "mobile" => $this->mobile,
            "ancestors" => $this->whenLoaded('ancestors', new CategoryResource($this->ancestors)),
            "brand" => $this->whenLoaded('brand', $this->brand),
            // "category_brands" => $this->whenLoaded('category_brands', $this->category_brands),
            $this->mergeWhen($this->relationLoaded('translations'), [
                "description" => $this->description,
                "meta_title" => $this->meta_title,
                "meta_keyword" => $this->meta_keyword,
                "meta_description" => $this->meta_description,
                "translations" => CategoryTranslationResource::collection($this->translations),
            ]),
        ];
    }
}
