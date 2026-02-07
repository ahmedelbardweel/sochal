<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveViewerCountUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $liveId;
    public $viewerCount;

    public function __construct($liveId, $viewerCount)
    {
        $this->liveId = $liveId;
        $this->viewerCount = $viewerCount;
    }

    public function broadcastOn()
    {
        return new Channel('live.' . $this->liveId);
    }

    public function broadcastAs()
    {
        return 'viewer.count.updated';
    }
}
