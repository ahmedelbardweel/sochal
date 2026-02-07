<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'title',
        'visibility',
        'status',
        'room_key',
        'channel_name',
        'started_at',
        'ended_at',
        'max_viewers',
        'is_recording',
        'recording_state',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_recording' => 'boolean',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LiveParticipant::class, 'live_id');
    }

    public function activeParticipants(): HasMany
    {
        return $this->hasMany(LiveParticipant::class, 'live_id')->whereNull('left_at');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LiveComment::class, 'live_id');
    }

    public function reactionBuckets(): HasMany
    {
        return $this->hasMany(LiveReactionBucket::class, 'live_id');
    }

    public function bans(): HasMany
    {
        return $this->hasMany(LiveBan::class, 'live_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'source_live_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'live');
    }
}
