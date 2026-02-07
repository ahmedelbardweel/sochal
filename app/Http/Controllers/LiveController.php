<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use App\Models\LiveParticipant;
use App\Models\LiveComment;
use App\Models\LiveReactionBucket;
use App\Models\LiveBan;
use App\Services\LiveAuthService;
use App\Events\LiveEnded;
use App\Events\LiveCommentCreated;
use App\Events\LiveReactionBucketUpdated;
use App\Events\LiveViewerCountUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LiveController extends Controller
{
    protected $authService;

    public function __construct(LiveAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function start(Request $request)
    {
        $user = Auth::user();

        // Automatically end existing live sessions for this host
        LiveSession::where('host_id', $user->id)
            ->where('status', 'live')
            ->update([
                'status' => 'ended',
                'ended_at' => now(),
            ]);
        
        // Mark participants of those sessions as left
        LiveParticipant::whereIn('live_id', function($query) use ($user) {
                $query->select('id')
                    ->from('live_sessions')
                    ->where('host_id', $user->id)
                    ->where('status', 'ended');
            })
            ->whereNull('left_at')
            ->update(['left_at' => now()]);

        $live = LiveSession::create([
            'host_id' => $user->id,
            'title' => $request->input('title', $user->username . '\'s Live'),
            'visibility' => $request->input('visibility', 'public'),
            'status' => 'live',
            'channel_name' => 'live_' . Str::random(10),
            'started_at' => now(),
        ]);

        LiveParticipant::updateOrCreate(
            ['live_id' => $live->id, 'user_id' => $user->id],
            ['role' => 'host', 'joined_at' => now(), 'left_at' => null]
        );

        return response()->json([
            'liveId' => $live->id,
            'roomKey' => $live->room_key,
            'channelName' => $live->channel_name,
            'wsSignalingUrl' => config('services.live.signaling_url'),
            'iceServers' => config('services.live.ice_servers'),
            'token' => $this->authService->generateLiveToken($live->id, $user->id, 'host'),
        ]);
    }

    public function join($id)
    {
        $user = Auth::user();
        $live = LiveSession::findOrFail($id);

        if ($live->status !== 'live') {
            return response()->json(['error' => 'Session is not live'], 403);
        }

        // Check if banned
        if (LiveBan::where('live_id', $live->id)->where('user_id', $user->id)->exists()) {
            return response()->json(['error' => 'You are banned from this session'], 403);
        }

        LiveParticipant::updateOrCreate(
            ['live_id' => $live->id, 'user_id' => $user->id],
            ['role' => 'audience', 'joined_at' => now(), 'left_at' => null]
        );

        return response()->json([
            'liveId' => $live->id,
            'channelName' => $live->channel_name,
            'wsSignalingUrl' => config('services.live.signaling_url'),
            'iceServers' => config('services.live.ice_servers'),
            'token' => $this->authService->generateLiveToken($live->id, $user->id, 'audience'),
        ]);
    }

    public function leave($id)
    {
        $user = Auth::user();
        LiveParticipant::where('live_id', $id)
            ->where('user_id', $user->id)
            ->update(['left_at' => now()]);

        return response()->json(['message' => 'Left successfully']);
    }

    public function end($id)
    {
        $user = Auth::user();
        $live = LiveSession::where('id', $id)->where('host_id', $user->id)->firstOrFail();

        $live->update([
            'status' => 'ended',
            'ended_at' => now(),
        ]);

        // Mark all participants as left
        LiveParticipant::where('live_id', $live->id)
            ->whereNull('left_at')
            ->update(['left_at' => now()]);

        return response()->json(['message' => 'Live session ended']);
    }

    public function comment(Request $request, $id)
    {
        $user = Auth::user();
        $live = LiveSession::findOrFail($id);

        if ($live->status !== 'live') {
            return response()->json(['error' => 'Session is not live'], 403);
        }

        $comment = LiveComment::create([
            'live_id' => $live->id,
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'created_at' => now(),
        ]);

        return response()->json($comment);
    }

    public function react(Request $request, $id)
    {
        $user = Auth::user();
        $reaction = $request->input('reaction', 'heart');
        
        // Bucket 2 seconds
        $bucketTs = Carbon::now()->startOfSecond();
        if ($bucketTs->second % 2 !== 0) {
            $bucketTs->subSecond();
        }

        $bucket = LiveReactionBucket::firstOrCreate(
            ['live_id' => $id, 'bucket_ts' => $bucketTs, 'reaction' => $reaction],
            ['count' => 0]
        );

        $bucket->increment('count');

        return response()->json(['status' => 'ok']);
    }

    public function feed()
    {
        $lives = LiveSession::with('host')
            ->where('status', 'live')
            ->orderBy('started_at', 'desc')
            ->get();

        return response()->json($lives);
    }

    public function show($id)
    {
        $live = LiveSession::with(['host', 'activeParticipants.user'])->findOrFail($id);
        return response()->json($live);
    }
}
