<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'telegram_message_id',
        'content_json',
        'content_html',
        'is_advertisement',
        'is_draft'
    ];

    protected $casts = [
        'content_json' => 'array',
        'is_advertisement' => 'boolean',
        'is_draft' => 'boolean',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}

