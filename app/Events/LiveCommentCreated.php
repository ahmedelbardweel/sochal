<?php

namespace App\Events;

use App\Models\LiveComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveCommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(LiveComment $comment)
    {
        $this->comment = $comment->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('live.' . $this->comment->live_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'comment.created';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'comment',
            'liveId' => $this->comment->live_id,
            'comment' => [
                'id' => $this->comment->id,
                'userId' => $this->comment->user_id,
                'username' => $this->comment->user->username,
                'avatar' => $this->comment->user->avatar_url,
                'message' => $this->comment->message,
                'createdAt' => $this->comment->created_at->toIso8601String(),
            ],
        ];
    }
}
