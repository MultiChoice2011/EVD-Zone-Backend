<?php

namespace App\Http\Resources\Seller\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductAccountDetailsResource extends JsonResource
{
    /**
     * Category data into an array.
     *
     */
    public function toArray($request)
    {
        return [
            'name' => $this->resource['name'] ?? null,
            'avatar' => $this->resource['avatar'] ?? null,
        ];
    }
}
