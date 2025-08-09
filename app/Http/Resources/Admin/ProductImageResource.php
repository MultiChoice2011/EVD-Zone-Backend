<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Front\ProductResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "product_id" => $this->product_id,
            "image" => $this->image,
        ];
    }
}
