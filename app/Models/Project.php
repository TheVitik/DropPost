<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'timezone'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(ProjectUser::class)
            ->withPivot(['role', 'permissions', 'is_invited']);
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function bots(): HasMany
    {
        return $this->hasMany(AIBot::class);
    }

    public function postTemplates(): HasMany
    {
        return $this->hasMany(PostTemplate::class);
    }
}
