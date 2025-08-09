<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'account_number' => $this->account_number,
            'iban_number' => $this->iban_number,
            'translations' => $this->whenLoaded('translations',$this->translations) ?? [],
            'countries' => $this->whenLoaded('countries',$this->countries) ?? [],
        ];
    }
}
