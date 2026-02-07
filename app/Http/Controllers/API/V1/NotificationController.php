<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List user notifications
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(30);
        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read']);
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        $request->user()
            ->notifications()
            ->where('id', $id)
            ->delete();

        return response()->json(['message' => 'Notification deleted']);
    }
}
