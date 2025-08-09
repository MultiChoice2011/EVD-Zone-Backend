<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "order_count" => $this->order_count,
            "total_value_of_orders" => $this->total_value_of_orders,
            "total_of_wallets" => $this->total_of_wallets,
            "wallet_cash_transactions" => RechargeBalanceResource::collection($this->wallet_transactions_cash),
            'support_tickets' => TicketSystemResource::collection($this->tickets_pending)
        ];
    }
}
