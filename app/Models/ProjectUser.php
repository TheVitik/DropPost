<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class ProjectUser extends Pivot
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $with = ['project'];

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'user_id',
        'permissions',
        'role',
        'is_invited'
    ];

    protected $casts = [
        'permissions' => 'array'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });

        Pivot::creating(function($pivot) {
            $pivot->id = Str::uuid();
        });
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
