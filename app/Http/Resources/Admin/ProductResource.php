<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        $attributes = $this->resource->toArray();
        return [
            "id" => $this->id,
            "name" => $this->name,
            "image" => $this->image,
            "quantity" => $this->quantity,
            "sold_quantity" => $this->sold_quantity,
            "status" => $this->status,
            "cost_price" => $this->cost_price,
            "price" => $this->price,
            "wholesale_price" => $this->wholesale_price,
            "type" => $this->type,
            "tax_id" => $this->tax_id,
            "tax_type" => $this->tax_type,
            "tax_amount" => $this->tax_amount,
            "is_live_integration" => $this->is_live_integration,
            "web" => $this->web,
            "mobile" => $this->mobile,
            "vendor" => $this->when( array_key_exists('vendor', $attributes), function () use($attributes){
                if (is_null($attributes['vendor']))
                    return null;
                return ["id" => $this->vendor->id, "name" => $this->vendor->name];
            } ),
            "brand" => $this->when( array_key_exists('brand', $attributes), function () use($attributes){
                if (is_null($attributes['brand']))
                    return null;
                return ["id" => $this->brand->id, "name" => $this->brand->name];
            } ),
//            "category" => $this->when( array_key_exists('category', $attributes), function () use($attributes){
//                if (is_null($attributes['category']))
//                    return null;
//                return ["id" => $this->category->id, "name" => $this->category->name];
//            } ),
            "categories" => $this->when( array_key_exists('categories', $attributes), ProductCategoryResource::collection($this->whenLoaded('categories')) ),
            "product_discount_seller_group" => $this->when( array_key_exists('product_discount_seller_group', $attributes), $this->whenLoaded('productDiscountSellerGroup') ),
            "product_price_seller_group" => $this->when( array_key_exists('product_price_seller_group', $attributes), $this->whenLoaded('productPriceSellerGroup') ),

        ];
    }
}
