<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'telegram_message_id' => $this->telegram_message_id,
            'channel' => new ChannelListResource($this->channel),
            'content_html' => $this->content_html,
            'content_json' => $this->content_json,
            'is_advertisement' => $this->is_advertisement,
            'is_draft' => $this->is_draft,
            'publish_at' => $this->publish_at,
            'delete_at' => $this->delete_at,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
