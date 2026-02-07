<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'caption',
        'privacy', // public, followers, private
        'comments_disabled',
        'hide_like_count',
        'location',
        'likes_count',
        'comments_count',
        'shares_count',
        'views_count',
        'status', // active, hidden, deleted
        'moderation_reason',
        'type', // post, reel, video
    ];

    protected $casts = [
        'comments_disabled' => 'boolean',
        'hide_like_count' => 'boolean',
    ];

    protected $appends = ['is_liked'];

    public function getIsLikedAttribute()
    {
        // PERFORMANCE BOOST: If we eager-loaded withExists(['likes as is_liked']), use it!
        if (array_key_exists('is_liked', $this->attributes)) {
            return (bool) $this->attributes['is_liked'];
        }

        $userId = auth('sanctum')->id();
        if (!$userId) return false;
        
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(PostMedia::class)->orderBy('sort_order');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function views()
    {
        return $this->hasMany(PostView::class);
    }

    public function hashtags()
    {
        return $this->belongsToMany(Hashtag::class, 'post_hashtags');
    }
}
