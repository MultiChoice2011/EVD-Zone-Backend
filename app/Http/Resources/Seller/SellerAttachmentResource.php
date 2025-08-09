<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SellerAttachmentResource extends JsonResource
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
            'file_url' => $this->file_url,
            'type' => $this->type,
            'extension' => $this->extension,
            'size' => $this->size,
            'created_at' => $this->created_at
        ];
    }
}
