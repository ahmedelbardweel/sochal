<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostView;
use App\Services\Post\PostService;
use App\Services\Post\DiscoveryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected $postService;
    protected $discoveryService;

    public function __construct(PostService $postService, DiscoveryService $discoveryService)
    {
        $this->postService = $postService;
        $this->discoveryService = $discoveryService;
    }

    /**
     * Display a listing of posts (Feed).
     */
    public function index(Request $request)
    {
        $filters = $request->only(['user_id']);
        $posts = $this->postService->getFeed($request->user(), $filters);
        
        return response()->json($posts);
    }

    /**
     * Algorithmic Discovery Feed
     */
    public function discovery(Request $request)
    {
        \Log::info('API: Discovery Feed Request');
        try {
            $posts = $this->discoveryService->getDiscoveryFeed(15, $request->query('type'));
            return response()->json($posts);
        } catch (\Exception $e) {
            \Log::error('API: Discovery Feed Error', ['msg' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get posts from followed users
     */
    public function following(Request $request)
    {
        $posts = $this->postService->getFeed($request->user(), ['following_only' => true]);
        return response()->json($posts);
    }

    /**
     * Log a view for a post
     */
    public function logView(Request $request, Post $post)
    {
        $user = $request->user('sanctum');
        
        // De-duplication check: Don't log if same user viewed in last hour
        $exists = PostView::where('post_id', $post->id)
            ->where(function($q) use ($user, $request) {
                if ($user) $q->where('user_id', $user->id);
                else $q->where('ip_address', $request->ip());
            })
            ->where('created_at', '>', now()->subHour())
            ->exists();

        if (!$exists) {
            PostView::create([
                'post_id' => $post->id,
                'user_id' => $user?->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            $post->increment('views_count');
        }

        return response()->json(['status' => 'logged']);
    }

    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        Log::info('Post Store Request:', [
            'type' => $request->input('type'),
            'has_media' => $request->hasFile('media'),
            'has_thumbnail' => $request->hasFile('thumbnail'),
            'all' => $request->all(),
            'files' => array_keys($request->allFiles()),
        ]);

        try {
            $request->validate([
                'caption' => 'nullable|string|max:2200',
                'privacy' => 'nullable|in:public,followers,private',
                'location' => 'nullable|string|max:100',
                'media' => 'required',
                'thumbnail' => 'nullable|image|max:5120',
                'type' => 'nullable|in:post,reel,video', // Validate type
            ]);

            // Default type to 'post' if not provided
            $type = $request->input('type', 'post');

            // Normalize media to an array if it's single file
            $mediaFiles = $request->file('media');
            if (!is_array($mediaFiles)) {
                $mediaFiles = [$mediaFiles];
            }

            // Handle Thumbnails
            $thumbnails = [];
            if ($request->hasFile('thumbnail')) {
                $thumbFile = $request->file('thumbnail');
                if (is_array($thumbFile)) {
                    $thumbnails = $thumbFile;
                } else {
                    $thumbnails = [$thumbFile];
                }
            }

            $post = $this->postService->createPost(
                $request->user(),
                $request->only(['caption', 'privacy', 'location']),
                $mediaFiles,
                $request->has('media_duration') ? (array) $request->input('media_duration') : [],
                $thumbnails, // Pass thumbnails
                $type // Pass Type
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Post Validation Failed:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Post Store Error:', ['msg' => $e->getMessage()]);
            return response()->json(['message' => 'Transmission failed: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        return response()->json([
            'data' => $post->load(['user', 'media', 'hashtags'])
        ]);
    }

    /**
     * Update the specified post (Edit Caption/Privacy).
     */
    public function update(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'caption' => 'nullable|string|max:2200',
            'privacy' => 'nullable|in:public,followers,private',
            'location' => 'nullable|string|max:100',
        ]);

        $post->update($request->only(['caption', 'privacy', 'location']));

        // Re-process hashtags if caption changed
        if ($request->has('caption')) {
            $this->postService->updateHashtags($post);
        }

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post->load('media', 'user')
        ]);
    }

    /**
     * Hide the specified post from public view (Owner action).
     */
    public function hide(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->update(['status' => 'hidden']);

        return response()->json([
            'message' => 'Post hidden from feed',
            'status' => 'hidden'
        ]);
    }

    /**
     * Unhide the specified post (Owner action).
     */
    public function unhide(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->update(['status' => 'active']);

        return response()->json([
            'message' => 'Post restored to feed',
            'status' => 'active'
        ]);
    }

    /**
     * Remove the specified post.
     */
    public function destroy(Request $request, Post $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
