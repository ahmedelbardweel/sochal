<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Services\Media\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * List user chats
     */
    public function index(Request $request)
    {
        $chats = $this->chatService->getUserChats($request->user());
        return response()->json($chats);
    }

    /**
     * Start/fetch direct chat
     */
    public function startDirectChat(Request $request, User $user)
    {
        $chat = $this->chatService->getDirectChat($request->user(), $user);
        return response()->json(['data' => $chat->load('users')]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request, Chat $chat)
    {
        // Check if user is member
        if (!$chat->members()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required_without:media_url|string|max:5000',
            'type' => 'nullable|string|in:text,image,video,post_share,story_reply',
            'media_url' => 'nullable|string',
            'metadata' => 'nullable|array',
        ]);

        $message = $this->chatService->sendMessage($request->user(), $chat, $request->all());
        \Illuminate\Support\Facades\Cache::forget("chat.{$chat->id}.typing.{$request->user()->id}");

        // Broadcast to real-time engine
        try {
            broadcast(new \App\Events\MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            \Log::warning("Real-time broadcast failed, but message was saved: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }

    /**
     * Get messages for a chat
     */
    public function messages(Request $request, Chat $chat)
    {
        if (!$chat->members()->where('user_id', $request->user()->id)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()->with('user')->latest()->paginate(50);
        
        // Polling Fallback: Check for other typing users via members list
        $typingUser = null;
        $chat->load('members');
        foreach ($chat->members as $member) {
            if ($member->user_id != $request->user()->id) {
                $status = \Illuminate\Support\Facades\Cache::get("chat.{$chat->id}.typing.{$member->user_id}");
                if ($status) {
                    $typingUser = $status;
                    break;
                }
            }
        }

        $response = $messages->toArray();
        $response['typing'] = $typingUser;
        return response()->json($response);
    }

    /**
     * Send typing status pulse
     */
    public function sendTyping(Request $request, Chat $chat)
    {
        if (!$chat->members()->where('user_id', $request->user()->id)->exists()) {
             return response()->json(['message' => 'Unauthorized'], 403);
        }
        // Cache name for 7 seconds to survive poll jitter
        \Illuminate\Support\Facades\Cache::put("chat.{$chat->id}.typing.{$request->user()->id}", $request->user()->display_name, 7);
        return response()->json(['status' => 'success']);
    }
}
