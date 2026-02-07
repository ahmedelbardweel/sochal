<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'type', // image, video, text
        'media_url',
        'thumbnail_url',
        'text_content',
        'background_color',
        'duration',
        'views_count',
        'replies_count',
        'expires_at',
        'mentions',
        'source_live_id', // Added this line
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'mentions' => 'array',
    ];

    protected $appends = ['created_at_human'];

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : 'Just now';
    }

    public function getMediaUrlAttribute($value)
    {
        if (empty($value)) return $value;
        if (str_starts_with($value, 'http')) {
            $path = parse_url($value, PHP_URL_PATH);
            if ($path && str_starts_with($path, '/storage/')) {
                return asset($path);
            }
            return $value;
        }
        return asset('storage/' . $value);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function views()
    {
        return $this->hasMany(StoryView::class);
    }

    public function sourceLiveSession()
    {
        return $this->belongsTo(LiveSession::class, 'source_live_id');
    }
}
