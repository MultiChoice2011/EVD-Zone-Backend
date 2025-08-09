<?php

namespace App\Http\Resources\Seller;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminListResource extends JsonResource
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
            'name' => $this->name,
            'owner_name' => $this->owner_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo'  => $this->logo,
            'status' => $this->status,
            'approvalStatus' => $this->approval_status,
            'parent_id' => $this->parent_id,
            'roles' => $this->getRoleNames(),
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
