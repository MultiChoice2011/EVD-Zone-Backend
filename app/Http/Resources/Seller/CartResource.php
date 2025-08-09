<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'seller_id' => $this->owner_id,
            'seller_name' => $this->owner?->name,
            'cart_price' => $this->cart_price,
            'tax_rate' => $this->tax_rate,
            'total_price' => $this->total_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cart_products' => CartProductResource::collection($this->whenLoaded('cartProducts')),
        ];
    }
}
