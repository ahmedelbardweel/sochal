<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveReactionBucket extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'live_id',
        'bucket_ts',
        'reaction',
        'count',
    ];

    protected $casts = [
        'bucket_ts' => 'datetime',
    ];

    public function live(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_id');
    }
}
