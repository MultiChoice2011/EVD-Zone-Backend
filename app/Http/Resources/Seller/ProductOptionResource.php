<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOptionResource extends JsonResource
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
            'product_id' => $this->product_id,
            'option_id' => $this->option_id,
            'value' => $this->value,
            'required' => $this->required,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'option' => new OptionResource($this->whenLoaded('option')),
        ];
    }
}
