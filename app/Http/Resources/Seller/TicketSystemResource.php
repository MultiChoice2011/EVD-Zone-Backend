<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketSystemResource extends JsonResource
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
            "title" => $this->title,
            "status" => $this->status,
            "details" => $this->details,
            "orderNumber" => $this->whenLoaded("order",$this->order?->id),
            'replies' => $this->whenLoaded('replies',TicketReplayResource::collection($this->replies)),
            "created_at" => $this->created_at,
            'attachment' => $this->whenLoaded('supportTicketsAttachments',SupportTicketAttachmentResource::make($this->supportTicketsAttachments)),
            'error_message' => $this->error_message
        ];
    }
}
