<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $with = ['project', 'user'];

    protected $fillable = [
        'project_id',
        'username',
        'telegram_user_id'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
            $model->hash = bin2hex(random_bytes(32));
        });
    }

    public function getRouteKey(): string
    {
        return 'hash';
    }

    // Custom methods

    public function accept(): void
    {
        $this->accepted_at = now();
        $this->save();
    }

    public function decline(): void
    {
        $this->delete();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
