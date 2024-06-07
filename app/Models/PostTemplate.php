<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostTemplate extends Model
{
    use HasFactory;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'content_json',
        'content_html'
    ];

    protected $casts = [
        'content_json' => 'array'
    ];

    public static function boot() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function setContentHtmlAttribute($value)
    {
        $this->attributes['content_html'] = $value;
        $this->attributes['content_json'] = json_encode([], true);
    }
}
