<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\Hashtag;
use App\Models\User;
use App\Services\Media\MediaUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostService
{
    protected $mediaService;

    public function __construct(MediaUploadService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function createPost(User $user, array $data, $mediaFiles = [], $durations = [], $thumbnails = [], $type = 'post')
    {
        return DB::transaction(function () use ($user, $data, $mediaFiles, $durations, $thumbnails, $type) {
            $post = $user->posts()->create([
                'caption' => $data['caption'] ?? null,
                'privacy' => $data['privacy'] ?? 'public',
                'location' => $data['location'] ?? null,
                'type' => $type,
            ]);

            // Handle Media
            foreach ($mediaFiles as $index => $file) {
                $upload = $this->mediaService->upload($file);
                
                $thumbnailPath = null;
                if (isset($thumbnails[$index])) {
                    $thumbUpload = $this->mediaService->upload($thumbnails[$index], 'posts/thumbnails');
                    $thumbnailPath = $thumbUpload['path'];
                }

                $post->media()->create([
                    'type' => Str::contains($upload['mime_type'], 'video') ? 'video' : 'image',
                    'url' => $upload['path'],
                    'thumbnail_url' => $thumbnailPath,
                    'mime_type' => $upload['mime_type'],
                    'file_size' => $upload['file_size'],
                    'duration' => $durations[$index] ?? null,
                    'sort_order' => $index,
                    'status' => 'processed', 
                ]);
            }

            // Extract Hashtags
            $this->processHashtags($post);

            return $post->load('media', 'user');
        });
    }

    private function processHashtags(Post $post)
    {
        if (!$post->caption) return;

        preg_match_all('/#(\w+)/', $post->caption, $matches);
        $hashtagNames = array_unique($matches[1]);

        foreach ($hashtagNames as $name) {
            $hashtag = Hashtag::firstOrCreate(['name' => strtolower($name)]);
            $post->hashtags()->syncWithoutDetaching([$hashtag->id]);
            
            // Increment count
            $hashtag->increment('posts_count');
            $hashtag->update(['last_used_at' => now()]);
        }
    }

    public function getFeed(User $user, array $filters = [])
    {
        $query = Post::with(['user' => function($q) use ($user) {
                // Ensure followers_count and posts_count are loaded for profile-like hover/cards if needed
                $q->withCount(['followers', 'posts']);
                if ($user) {
                    $q->withExists(['followers as is_following' => function($f) use ($user) {
                        $f->where('follower_id', $user->id)->where('follows.status', 'accepted');
                    }]);
                    $q->withExists(['followers as has_pending_request' => function($f) use ($user) {
                        $f->where('follower_id', $user->id)->where('follows.status', 'pending');
                    }]);
                }
            }, 'media'])
            ->where(function($q) use ($user, $filters) {
                // Default: Active posts only
                $q->where('status', 'active');

                // Exception: If viewing specific user's posts AND viewer is that user, allow hidden
                if ($user && isset($filters['user_id']) && $user->id == $filters['user_id']) {
                     $q->orWhere('status', 'hidden');
                }
            });

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        } elseif (isset($filters['following_only']) && $filters['following_only']) {
            // Strictly Following
            $followingIds = $user ? $user->following()->wherePivot('status', 'accepted')->pluck('following_id') : collect();
            $query->whereIn('user_id', $followingIds);
        } else {
            // Mixed feed: Following + Public
            $followingIds = $user ? $user->following()->wherePivot('status', 'accepted')->pluck('following_id') : collect();
            $query->where(function($q) use ($followingIds) {
                $q->whereIn('user_id', $followingIds)
                  ->orWhere('privacy', 'public');
            });
        }

        $posts = $query->withExists(['likes as is_liked' => function($q) use ($user) {
                $q->where('user_id', $user?->id);
            }])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(15);

        // Standarize the boolean (Laravel might return 1/0 from DB)
        $posts->getCollection()->transform(function($post) {
            $post->is_liked = (bool) $post->is_liked; 
            return $post;
        });

        return $posts;
    }

    public function updateHashtags(Post $post)
    {
        // Decrement old hashtags
        foreach ($post->hashtags as $hashtag) {
            $hashtag->decrement('posts_count');
        }

        $post->hashtags()->detach();

        // Process new ones
        $this->processHashtags($post);
    }
}
