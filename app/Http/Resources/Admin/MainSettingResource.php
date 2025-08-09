<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class MainSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "key" => $this->key,
            "plain_value" => $this->plain_value,
            "model" => $this->model,
            "translations" => $this->translations,
        ];
    }
}
