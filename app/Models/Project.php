<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'timezone'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users');
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function bots(): HasMany
    {
        return $this->hasMany(AIBot::class);
    }
}
