<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveId;

    public function __construct($liveId)
    {
        $this->liveId = $liveId;
    }

    public function broadcastOn()
    {
        return new Channel('live.' . $this->liveId);
    }

    public function broadcastAs()
    {
        return 'live.ended';
    }
}
