<?php

namespace App\Http\Resources;

use App\Helpers\FileHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->id,
            'image' => FileHelper::getModelImageUrl($this),
            'permissions' => PermissionResource::collection($this->role->permissions),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
