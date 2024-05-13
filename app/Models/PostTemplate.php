<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content_json',
        'content_html'
    ];

    protected $casts = [
        'content_json' => 'array'
    ];
}
