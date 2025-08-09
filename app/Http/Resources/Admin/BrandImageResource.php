<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "brand_id" => $this->brand_id,
            "key" => $this->key,
            "image" => $this->image,
        ];
    }
}
