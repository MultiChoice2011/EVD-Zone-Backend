<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class VendorResource extends JsonResource
{
    public function toArray($request)
    {
        $attributes = $this->resource->toArray();
        return [
            "id" => $this->id,
            "name" => $this->name,
            "owner_name" => $this->owner_name,
            "logo" => $this->logo,
            "phone" => $this->phone,
            "status" => $this->status,
            'is_service' => $this->is_service,
            "country_id" => $this->country_id,
            "city_id" => $this->city_id,
            "region_id" => $this->region_id,
            //"image_attach" => $this->when($this->image_attach, $this->image_attach),
            "email" => $this->when( $this->email, $this->email ),
            "serial_number" => $this->when( $this->serial_number, $this->serial_number ),
            "description" => $this->when( $this->description, $this->description ),
            "integration_id" => $this->when( $this->integration_id, $this->integration_id ),
            "street" => $this->when( $this->street, $this->street ),
            "country" => $this->when( $this->country, new CountryResource($this->whenLoaded('country')) ),
            "region" => $this->when( $this->region, new RegionResource($this->whenLoaded('region')) ),
            "city" => $this->when( $this->city, new CityResource($this->whenLoaded('city')) ),
            "attachments" => $this->when( $this->attachments, VendorAttachmentResource::collection($this->whenLoaded('attachments')) ),
        ];
    }
}
