<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AIBotResource extends JsonResource
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
            'name' => $this->topic . " ($this->id)",
            'topic' => $this->topic,
            'keywords' => $this->keywords,
            'prompt' => $this->prompt,
            'post_template_id' => $this->post_template_id,
            'is_generated_photos' => $this->is_generated_photos,
            'is_real_photos' => $this->is_real_photos,
            'post_planning' => $this->post_planning,
        ];
    }
}
