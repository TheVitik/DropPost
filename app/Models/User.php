<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'photo_url',
        'telegram_user_id',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function isMemberOfProject(Project $project): bool
    {
        return $this->projects->contains($project);
    }

    public function userProjects(): HasMany
    {
        return $this->hasMany(ProjectUser::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->using(ProjectUser::class);
    }
}
