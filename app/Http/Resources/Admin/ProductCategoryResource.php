<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ProductCategoryResource extends JsonResource
{
    /**
     * Category data into an array.
     *
     */
    public function toArray($request)
    {
        $attributes = $this->resource->toArray();
        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->image,
            "parent_id" => $this->parent_id,
            "level" => $this->level,
            "status" => $this->status,
            "is_topup" => $this->is_topup,
            "web" => $this->web,
            "mobile" => $this->mobile,
            "category_brands" => $this->when( array_key_exists('category_brands', $attributes), $this->category_brands)
        ];
    }
}
