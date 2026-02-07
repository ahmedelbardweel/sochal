<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationReceived;

class NotificationService
{
    /**
     * Send a notification to a user
     */
    public function send(User $user, string $type, $notifiable = null, array $data = [])
    {
        // Don't notify yourself
        if (isset($data['sender_id']) && $data['sender_id'] == $user->id) {
            return null;
        }

        $notification = Notification::create([
            'user_id' => $user->id,
            'sender_id' => $data['sender_id'] ?? null,
            'type' => $type,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id' => $notifiable ? $notifiable->id : null,
            'data' => $data,
            'read_at' => null,
        ]);

        // Load sender for broadcast if exists
        if (isset($data['sender_id'])) {
            $notification->load('sender');
        }

        try {
            broadcast(new NotificationReceived($notification))->toOthers();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("BROADCAST_FAILED: " . $e->getMessage());
        }

        return $notification;
    }
}
