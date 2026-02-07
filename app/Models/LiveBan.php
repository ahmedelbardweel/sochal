<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveBan extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'live_id',
        'user_id',
        'banned_by',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function live(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}
