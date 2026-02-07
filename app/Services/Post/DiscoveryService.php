<?php

namespace App\Services\Post;

use App\Models\Post;
use Illuminate\Support\Facades\DB;

class DiscoveryService
{
    /**
     * Get discovery feed sorted by engagement velocity (Hotness)
     * Formula: (likes * 5 + comments * 10 + views) / (hours_since_creation + 2)^1.5
     */
    public function getDiscoveryFeed($perPage = 20, $type = null)
    {
        $currentUser = auth('sanctum')->user();
        $posts = Post::with(['user' => function($q) use ($currentUser) {
                $q->withCount(['followers', 'posts']);
                if ($currentUser) {
                    $q->withExists(['followers as is_following' => function($f) use ($currentUser) {
                        $f->where('follower_id', $currentUser->id)->where('follows.status', 'accepted');
                    }]);
                    $q->withExists(['followers as has_pending_request' => function($f) use ($currentUser) {
                        $f->where('follower_id', $currentUser->id)->where('follows.status', 'pending');
                    }]);
                }
            }, 'media'])
            ->where('status', 'active')
            ->where('privacy', 'public')
            ->when($type, function($q) use ($type) {
                $q->whereHas('media', function($m) use ($type) {
                    $m->where('type', $type);
                });
            })
            ->latest()
            ->withExists(['likes as is_liked' => function($q) use ($currentUser) {
                $q->where('user_id', $currentUser?->id);
            }])
            ->cursorPaginate($perPage); 

        $posts->getCollection()->transform(function($post) {
            $post->is_liked = (bool) $post->is_liked;
            return $post;
        });

        return $posts;
    }
}
