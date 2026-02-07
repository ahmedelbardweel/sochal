<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveParticipant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'live_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
        'device_id',
        'ip_hash',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function live(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
