<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'type', // image, video
        'url',
        'variants',
        'thumbnail_url',
        'width',
        'height',
        'duration',
        'file_size',
        'mime_type',
        'alt_text',
        'status', // pending, processing, processed, failed
        'sort_order',
    ];

    protected $casts = [
        'variants' => 'array',
    ];

    public function getUrlAttribute($value)
    {
        if (empty($value)) return $value;
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return asset('storage/' . $value);
    }

    public function getThumbnailUrlAttribute($value)
    {
        if (empty($value)) return null;
        if (str_starts_with($value, 'http')) {
            return $value;
        }
        return asset('storage/' . $value);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
