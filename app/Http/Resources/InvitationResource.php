<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
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
            'project' => [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ],
            'user' => [
                'first_name' => $this->user->first_name,
            ],
            'accepted_at' => $this->accepted_at
        ];
    }
}
