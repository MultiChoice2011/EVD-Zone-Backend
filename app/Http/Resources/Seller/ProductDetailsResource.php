<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'price' => $this->price,
            'wholesale_price' => $this->wholesale_price,
            'image' => $this->image,
            'type' => $this->type,
            'is_available' => $this->is_available,
            'category_id' => $this->category_id,
            'is_max_quantity_one' => $this->is_max_quantity_one,
            'is_topup' => $this->is_topup,
            'profit_rate' => $this->profit_rate,
            'is_favorite' => $this->is_favorite, // Assumes you have a 'getIsFavoriteAttribute' accessor
            'name' => $this->name,
            'desc' => $this->desc,
            'meta_title' => $this->meta_title,
            'meta_keyword' => $this->meta_keyword,
            'meta_description' => $this->meta_description,
            'long_desc' => $this->long_desc,
            'content' => $this->content,
            'product_images' =>  ProductImageResource::collection($this->whenLoaded('product_images')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'product_options' => ProductOptionResource::collection($this->whenLoaded('product_options')),
        ];
    }
}
