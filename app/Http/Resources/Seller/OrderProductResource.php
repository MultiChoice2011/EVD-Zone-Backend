<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
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
            "type" => $this->type,
            "status" => $this->status,
            "total" => $this->total,
            "quantity" => $this->quantity,
            "unit_price" => $this->unit_price,
            "tax_value" => $this->tax_value,
            "product" => ProductResource::make($this->product),
            "brand" => $this->whenLoaded('brand',BrandResource::make($this->brand)),
        ];
    }
}
