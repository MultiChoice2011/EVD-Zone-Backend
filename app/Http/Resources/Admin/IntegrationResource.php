<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    /**
     * Category data into an array.
     *
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "keys" => $this->keys,
            "balance" => $this->balance,
            "vendor" => $this->vendor,
        ];
    }
}
