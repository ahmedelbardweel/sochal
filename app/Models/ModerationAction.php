<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'moderator_id',
        'target_type',
        'target_id',
        'action_type', // warn, delete, suspend, ban
        'reason',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function target()
    {
        return $this->morphTo();
    }
}
