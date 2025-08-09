<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
        ];
    }
}
