<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'name' => $this->name ?? '',
            'owner_name' => $this->owner_name ?? '',
            'email' => $this->email ?? '',
            'phone' => $this->phone ?? '',
            'approval_status' => $this->approval_status,
            'status' => $this->status,
            'country' => CountryResource::make($this->sellerAddress?->country),
            'city' => CityResource::make($this->sellerAddress?->city),
            'currency_id' => $this->currency_id,
            'currency' => CurrencyResource::make($this->currency),
            'address' => $this->address_details ??'',
            'created_at' => $this->created_at->format('Y-m-d') ?? '',
            'logo' => $this->logo,
            'balance' => $this->balance,
            'commercial_register_number' => $this->commercial_register_number,
            'tax_card_number' => $this->tax_card_number,
            'role' => $this->getRoleNames(),
            'permissions' => PermissionResource::collection($this->permissions),
            'attachments' => SellerAttachmentResource::collection($this->sellerAttachment) ?? [],
            'reject_reasons' => $this->whenLoaded('rejectReasons', SellerRejectReasonResource::collection($this->rejectReasons)) ?? [],
        ];
    }
}
