<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'posts_count',
        'trending_score',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'trending_score' => 'decimal:2',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_hashtags');
    }
}
