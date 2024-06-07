<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AIBot extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'ai_bots';

    protected $fillable = [
        'project_id',
        'topic',
        'keywords',
        'prompt',
        'post_template_id',
        'is_generated_photos',
        'is_real_photos',
        'post_planning',
    ];

    protected $casts = [
        'is_generated_photos' => 'boolean',
        'is_real_photos' => 'boolean',
        'post_planning' => 'array',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function setKeywordsAttribute($value): void
    {
        $this->attributes['keywords'] = implode(', ', $value);
    }

    public function getKeywordsAttribute($value): array
    {
        return explode(', ', $value);
    }

    public function postTemplate(): BelongsTo
    {
        return $this->belongsTo(PostTemplate::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }


}
