<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "created_at" => $this->created_at,
            "products_count" => $this->order_products->count() ?? 0,
            "total" => $this->total,
            "sub_total" => $this->sub_total,
            "status" => $this->status,
            "tax" => $this->tax,
            'vat' => $this->vat,
            "orderProducts" => $this->whenLoaded('order_products',OrderProductResource::collection($this->order_products)),
            'user' => $this->whenLoaded('owner',$this->owner),
            'currency' => $this->whenLoaded('currency',$this->currency)
        ];
    }
}
