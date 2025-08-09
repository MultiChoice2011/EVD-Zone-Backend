<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class VendorAttachmentResource extends JsonResource
{
    public function toArray($request)
    {
        $attributes = $this->resource->toArray();
        return [
            "vendor_id" => $this->vendor_id,
            "file_url" => $this->file_url,
            "extension" => $this->extension,
            "size" => $this->size,
            "created_at" => $this->created_at,
        ];
    }
}
