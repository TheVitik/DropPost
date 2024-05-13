<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'telegram_chat_id',
        'project_id',
        'members_count',
        'type',
        'username',
        'invite_link',
        'is_bot_active',
        'is_automessage_active',
        'is_copy_active',
        'ai_bot_id',
        'automessage',
        'copy_channel_username',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
