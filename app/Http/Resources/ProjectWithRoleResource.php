<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectWithRoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->project->id,
            'name' => $this->project->name,
            'timezone' => $this->project->timezone,
            'role' => $this->role,
            'permissions' => $this->permissions,
            'is_invited' => $this->is_invited
        ];
    }
}
