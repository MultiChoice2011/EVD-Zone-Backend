<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "parent_id" => $this->parent_id,
            "brand_id" => $this->brand_id,
            "name" => $this->name,
            "description" => $this->description,
            "is_topup" => $this->is_topup,
            "image" => $this->image,
            'child_count' => $this->child_count,
            "status" => $this->status,
            'parent' => $this->whenLoaded('parent', new CategoryResource($this->parent)),
            'brand' => $this->whenLoaded('brand', new BrandResource($this->brand)),
            // "has_brands" => $this->has_brands,
            // 'level' => $this->category?->level,
            // 'brand' => BrandResource::make($this->brand)
        ];
    }
}
