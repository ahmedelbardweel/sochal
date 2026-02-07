<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Bookmark;
use Illuminate\Support\Facades\DB;

class InteractionService
{
    protected $notificationService;

    public function __construct(\App\Services\NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Toggle like on a post
     */
    public function toggleLike(User $user, Post $post)
    {
        return DB::transaction(function () use ($user, $post) {
            // Lock for consistent reading - check if already liked
            $like = $post->likes()->where('user_id', $user->id)->sharedLock()->first();

            if ($like) {
                $post->likes()->where('user_id', $user->id)->delete();
                $liked = false;
            } else {
                $post->likes()->firstOrCreate(['user_id' => $user->id]);
                $liked = true;

                // Notify owner (except self)
                if ($post->user_id !== $user->id) {
                    $this->notificationService->send($post->user, 'like', $post, [
                        'sender_id' => $user->id,
                        'message' => "@{$user->username} liked your transmission."
                    ]);
                }
            }

            // High Efficiency Sync: Count absolute truth from indexed table
            $actualCount = $post->likes()->count();
            $post->update(['likes_count' => $actualCount]);

            return ['liked' => $liked, 'likes_count' => $actualCount];
        });
    }

    /**
     * Check if user likes a post
     */
    public function hasLiked(User $user, Post $post)
    {
        return $post->likes()->where('user_id', $user->id)->exists();
    }

    /**
     * Add a comment to a post
     */
    public function addComment(User $user, Post $post, array $data)
    {
        return DB::transaction(function () use ($user, $post, $data) {
            $comment = $post->comments()->create([
                'user_id' => $user->id,
                'comment' => $data['comment'],
                'parent_id' => $data['parent_id'] ?? null,
            ]);

            $post->increment('comments_count');

            // Notify owner
            $this->notificationService->send($post->user, 'comment', $post, [
                'sender_id' => $user->id,
                'message' => "@{$user->username} commented on your transmission."
            ]);

            return $comment->load('user');
        });
    }

    /**
     * Toggle bookmark on a post
     */
    public function toggleBookmark(User $user, Post $post)
    {
        $bookmark = Bookmark::where('user_id', $user->id)->where('post_id', $post->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $bookmarked = false;
        } else {
            Bookmark::create(['user_id' => $user->id, 'post_id' => $post->id]);
            $bookmarked = true;
        }

        return ['bookmarked' => $bookmarked];
    }
}
