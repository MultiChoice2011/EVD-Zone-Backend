<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Seller\OrderResource;
use App\Http\Resources\Seller\SellerResource;
use App\Http\Resources\Seller\SupportTicketAttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerComplaintsResource extends JsonResource
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
            'title' => $this->title,
            'message' => $this->details,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'customer' => $this->whenLoaded('customer',SellerResource::make($this->customer)),
            'order' => $this->whenLoaded('order',OrderResource::make($this->order)),
            'supportTicketsAttachments' => $this->whenLoaded('supportTicketsAttachments',SupportTicketAttachmentResource::make($this->supportTicketsAttachments)),
            'error_message' => $this->error_message
        ];
    }
}
