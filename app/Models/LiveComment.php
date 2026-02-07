<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveComment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'live_id',
        'user_id',
        'message',
        'created_at',
        'is_deleted',
        'deleted_by',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'is_deleted' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function live(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
