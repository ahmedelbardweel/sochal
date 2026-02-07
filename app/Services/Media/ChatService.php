<?php

namespace App\Services\Media; // Reusing or creating app/Services/Social

use App\Models\Chat;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatService
{
    /**
     * Start or get a direct chat
     */
    public function getDirectChat(User $user1, User $user2)
    {
        return DB::transaction(function () use ($user1, $user2) {
            // Find existing direct chat between these two
            $chat = Chat::where('type', 'direct')
                ->whereHas('members', function ($q) use ($user1) {
                    $q->where('user_id', $user1->id);
                })
                ->whereHas('members', function ($q) use ($user2) {
                    $q->where('user_id', $user2->id);
                })
                ->first();

            if (!$chat) {
                $chat = Chat::create(['type' => 'direct']);
                $chat->members()->createMany([
                    ['user_id' => $user1->id, 'role' => 'member'],
                    ['user_id' => $user2->id, 'role' => 'member'],
                ]);
            }

            return $chat;
        });
    }

    /**
     * Send a message
     */
    public function sendMessage(User $user, Chat $chat, array $data)
    {
        return DB::transaction(function () use ($user, $chat, $data) {
            $message = $chat->messages()->create([
                'sender_id' => $user->id,
                'type' => $data['type'] ?? 'text',
                'content' => $data['content'] ?? null,
                'media_url' => $data['media_url'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            $chat->update(['last_message_id' => $message->id]);

            // Real-time event would be dispatched here
            // broadcast(new MessageSent($message))->toOthers();

            return $message->load('user');
        });
    }

    /**
     * Get user chats
     */
    public function getUserChats(User $user)
    {
        return $user->belongsToMany(Chat::class, 'chat_members')
            ->with(['lastMessage.user', 'users'])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('messages.chat_id', 'chats.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(20);
    }
}
