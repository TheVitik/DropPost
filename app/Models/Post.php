<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'channel_id',
        'telegram_message_id',
        'content_json',
        'media',
        'content_html',
        'is_advertisement',
        'publish_at',
        'delete_at',
        'is_draft'
    ];

    protected $casts = [
        'content_json' => 'array',
        'is_advertisement' => 'boolean',
        'is_draft' => 'boolean',
        'publish_at' => 'datetime',
        'delete_at' => 'datetime',
        'media' => 'array'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}

