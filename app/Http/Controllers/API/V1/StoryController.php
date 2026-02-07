<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\StoryView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    /**
     * Get active stories for the feed (Following + Own)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $followingIds = $user->following()->pluck('following_id')->push($user->id);

        // Fetch active live sessions
        $liveSessions = \App\Models\LiveSession::with('host')
            ->whereIn('host_id', $followingIds)
            ->where('status', 'live')
            ->get()
            ->map(function ($live) {
                return [
                    'type' => 'live',
                    'live_id' => $live->id,
                    'user' => $live->host,
                    'title' => $live->title,
                    'is_live' => true,
                ];
            });
        
        $stories = Story::with(['user', 'views' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
            ->whereIn('user_id', $followingIds)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by user and add view status
        $grouped = $stories->groupBy('user_id')->map(function($userStories) {
            // Sort inner stories oldest to newest for chronological viewing
            $sortedStories = $userStories->sortBy('created_at')->values();
            
            $hasUnviewed = $sortedStories->filter(function($story) {
                return $story->views->isEmpty();
            })->isNotEmpty();
            
            return [
                'type' => 'story',
                'stories' => $sortedStories,
                'user' => $sortedStories->first()->user,
                'has_unviewed' => $hasUnviewed,
            ];
        })->values();

        // Merge Live sessions first
        $feed = $liveSessions->concat($grouped);

        return response()->json([
            'stories' => $feed
        ]);
    }

    /**
     * Store a new story
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:image,video,text',
                'image' => 'required_if:type,image|image|max:10240', // 10MB
                'text_content' => 'required_if:type,text|string|max:1000',
                'background_color' => 'nullable|string|max:20',
                'mentions' => 'nullable|string', // JSON string from frontend
            ]);

            $user = $request->user();
            $data = [
                'user_id' => $user->id,
                'type' => $request->type,
                'expires_at' => now()->addHours(24),
            ];

            if ($request->type === 'image' && $request->hasFile('image')) {
                $path = $request->file('image')->store('stories', 'public');
                $data['media_url'] = $path;
            } elseif ($request->type === 'text') {
                $data['text_content'] = $request->text_content;
                $data['background_color'] = $request->background_color ?? '#2D3FE6';
            }

            if ($request->has('mentions')) {
                $data['mentions'] = json_decode($request->mentions, true);
            }

            $story = Story::create($data);

            return response()->json([
                'message' => 'Story transmitted successfully',
                'story' => $story->load('user')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Story Validation Failed:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Story Store Error:', ['msg' => $e->getMessage()]);
            return response()->json(['message' => 'Transmission failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark a story as viewed
     */
    public function markAsViewed(Request $request, Story $story)
    {
        if ($story->expires_at < now()) {
            return response()->json(['message' => 'Story expired'], 410);
        }

        $user = $request->user();
        
        // Don't count own views
        if ($story->user_id === $user->id) {
            return response()->json(['message' => 'Own view']);
        }

        $exists = StoryView::where('story_id', $story->id)
            ->where('user_id', $user->id)
            ->exists();

        if (!$exists) {
            StoryView::create([
                'story_id' => $story->id,
                'user_id' => $user->id
            ]);
            $story->increment('views_count');
        }

        return response()->json(['message' => 'View recorded']);
    }
}
