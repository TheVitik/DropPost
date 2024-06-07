<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'telegram_chat_id' => $this->telegram_chat_id,
            'members_count' => $this->members_count,
            'type' => $this->type,
            'photo_path' => $this->photo_path,
            'username' => $this->username,
            'invite_link' => $this->invite_link,
            'is_bot_active' => $this->is_bot_active,
            'is_automessage_active' => $this->is_automessage_active,
            'is_copy_active' => $this->is_copy_active,
            'ai_bot_id' => $this->ai_bot_id,
            'automessage' => $this->automessage,
            'copy_channel_username' => $this->copy_channel_username,
        ];
    }
}
