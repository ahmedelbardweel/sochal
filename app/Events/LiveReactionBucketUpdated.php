<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveReactionBucketUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveId;
    public $bucketTs;
    public $reaction;
    public $count;

    /**
     * Create a new event instance.
     */
    public function __construct($liveId, $bucketTs, $reaction, $count)
    {
        $this->liveId = $liveId;
        $this->bucketTs = $bucketTs;
        $this->reaction = $reaction;
        $this->count = $count;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('live.' . $this->liveId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'reaction.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'type' => 'reaction_tick',
            'liveId' => $this->liveId,
            'bucketTs' => $this->bucketTs,
            'reaction' => $this->reaction,
            'count' => $this->count,
        ];
    }
}
