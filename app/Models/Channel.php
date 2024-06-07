<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Channel extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'telegram_chat_id',
        'project_id',
        'members_count',
        'type',
        'photo_path',
        'username',
        'invite_link',
        'is_bot_active',
        'is_automessage_active',
        'is_copy_active',
        'ai_bot_id',
        'automessage',
        'copy_telegram_chat_id',
    ];

    protected $casts = [
        'is_bot_active' => 'boolean',
        'is_automessage_active' => 'boolean',
        'is_copy_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function aiBot(): BelongsTo
    {
        return $this->belongsTo(AIBot::class);
    }
}
