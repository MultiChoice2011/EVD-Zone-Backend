<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this['name'],
            'owner_name' => $this['owner_name'],
            'email' => $this['email'],
            'country_id' => $this['country_id'],
            'city_id' => $this['city_id'],
            'phone' => $this['phone'],
//            'secret' => $this['secret'],
//            'qrCodeUrl' => $this['qrCodeUrl']
        ];
    }
}
