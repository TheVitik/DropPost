<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIBot extends Model
{
    use HasFactory;

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
    ];

    public function postTemplate(): BelongsTo
    {
        return $this->belongsTo(PostTemplate::class);
    }
}
