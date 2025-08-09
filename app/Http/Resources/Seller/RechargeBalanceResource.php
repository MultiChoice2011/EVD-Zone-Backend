<?php

namespace App\Http\Resources\Seller;

use App\Http\Resources\Admin\BankResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RechargeBalanceResource extends JsonResource
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
            'bank' => $this->whenLoaded('bank',BankResource::make($this->bank)),
            'seller' => $this->whenLoaded('seller',\App\Http\Resources\Admin\SellerResource::make($this->seller)),
            'currency' => $this->whenLoaded('currency', $this->currency),
            'recharge_balance_type' => $this->recharge_balance_type,
            'transferring_name' => $this->transferring_name,
            'transferring_account_number' => $this->transferring_account_number,
            'amount' => $this->amount,
            'receipt_image' => $this->ReceiptImageUrl,
            'type' => $this->type,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'status' => $this->status,
        ];
    }
}
