<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankCommissionSettingResource extends JsonResource
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
            'bank_commission_id' => $this->bank_commission_id,
            'name' => $this->name,
            'gate_fees' => $this->gate_fees,
            'static_value' => $this->static_value,
            'additional_value_fees' => $this->additional_value_fees
        ];
    }
}
