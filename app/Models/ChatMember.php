<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMember extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'chat_id',
        'user_id',
        'role', // member, admin
        'muted_until',
    ];

    protected $casts = [
        'muted_until' => 'datetime',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
