<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
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
            "code" => $this->code,
            "locale" => $this->locale,
            "image" => $this->image,
            "directory" => $this->directory,
            "status" => $this->status,
            "sort_order" => $this->sort_order
        ];
    }
}
