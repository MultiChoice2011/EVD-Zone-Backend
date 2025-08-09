<?php

namespace App\Http\Resources\Seller;

use App\Http\Resources\Admin\ProductCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = $this->resource->toArray();
        return [
            "id" => $this->id,
            "brand_id" => $this->brand_id,
            "name" => $this->name,
            "desc" => $this->desc,
            "meta_title" => $this->meta_title,
            "meta_keyword" => $this->meta_keyword,
            "meta_description" => $this->meta_description,
            "long_desc" => $this->long_desc,
            "content" => $this->content,
            "price" => $this->price,
            "image" => $this->image,
            "status" => $this->status,
            "type" => $this->type,
            "is_favorite" => $this->is_favorite,
            "is_available" => $this->is_available,
            "wholesale_price" => $this->wholesale_price,
            "tax_rate" => 0,
            "profit_rate" => $this->profit_rate,
            'discount' => $this->calcDiscount(),
            "product_images" => $this->when($this->product_images, ProductImageResource::collection($this->whenLoaded('product_images')) ),
            "brand" => $this->when( array_key_exists('brand', $attributes), function () use($attributes){
                if (is_null($attributes['brand']))
                    return null;
                return ["id" => $this->brand->id, "name" => $this->brand->name];
            } ),
            "categories" => $this->when( array_key_exists('categories', $attributes), ProductCategoryResource::collection($this->whenLoaded('categories')) ),
        ];
    }
}
