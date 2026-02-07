<?php

namespace App\Services\Post;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class FollowService
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Follow a user
     */
    public function follow(User $follower, User $following)
    {
        if ($follower->id === $following->id) {
            return ['error' => 'You cannot follow yourself'];
        }

        $existing = $follower->following()
            ->where('following_id', $following->id)
            ->first();

        if ($existing) {
            return ['status' => $existing->pivot->status];
        }

        \Illuminate\Support\Facades\Log::info("FOLLOW_PROCESS: @{$follower->username} -> @{$following->username}");
        \Illuminate\Support\Facades\Log::info("TARGET_PRIVACY: " . ($following->is_private ? 'PRIVATE' : 'PUBLIC'));

        $status = $following->is_private ? 'pending' : 'accepted';
        \Illuminate\Support\Facades\Log::info("RESULTING_STATUS: {$status}");

        $follower->following()->attach($following->id, [
            'status' => $status
        ]);

        // Notify
        $type = $status === 'pending' ? 'follow_request' : 'follow';
        $msg = $status === 'pending' ? "@{$follower->username} requested to follow you." : "@{$follower->username} started following you.";
        
        \Illuminate\Support\Facades\Log::info("SENDING_NOTIFICATION: Type: {$type}");

        $this->notificationService->send($following, $type, $follower, [
            'sender_id' => $follower->id,
            'message' => $msg
        ]);

        return ['status' => $status];
    }

    /**
     * Unfollow a user
     */
    public function unfollow(User $follower, User $following)
    {
        $follower->following()->detach($following->id);
        return ['status' => 'unfollowed'];
    }

    /**
     * Accept follow request
     */
    public function acceptRequest(User $user, User $follower)
    {
        $user->followers()->updateExistingPivot($follower->id, [
            'status' => 'accepted'
        ]);

        // Notify follower
        $this->notificationService->send($follower, 'follow_accept', $user, [
            'sender_id' => $user->id,
            'message' => "@{$user->username} accepted your follow request."
        ]);

        return ['status' => 'accepted'];
    }

    /**
     * Decline follow request
     */
    public function declineRequest(User $user, User $follower)
    {
        $user->followers()->detach($follower->id);
        return ['status' => 'declined'];
    }
}
