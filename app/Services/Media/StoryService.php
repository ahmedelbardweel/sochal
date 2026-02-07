<?php

namespace App\Services\Media; // Reusing namespace for simplicity or creating a new one

use App\Models\Story;
use App\Models\User;
use App\Models\StoryView;
use Illuminate\Support\Facades\Storage;

class StoryService
{
    /**
     * Create a new story
     */
    public function createStory(User $user, array $data, $file = null)
    {
        $mediaUrl = null;
        if ($file) {
            $path = $file->store('stories', 'public');
            $mediaUrl = Storage::disk('public')->url($path);
        }

        return Story::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'media_url' => $mediaUrl,
            'text_content' => $data['text_content'] ?? null,
            'background_color' => $data['background_color'] ?? null,
            'duration' => $data['duration'] ?? 15,
            'expires_at' => now()->addHours(24),
        ]);
    }

    /**
     * Mark story as viewed
     */
    public function viewStory(User $user, Story $story)
    {
        $view = StoryView::firstOrCreate([
            'story_id' => $story->id,
            'user_id' => $user->id,
        ]);

        if ($view->wasRecentlyCreated) {
            $story->increment('views_count');
        }

        return $view;
    }

    /**
     * Get active stories for feed
     */
    public function getFeedStories(User $user)
    {
        $followingIds = $user->following()->pluck('following_id');
        
        return Story::with('user')
            ->whereIn('user_id', $followingIds)
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->groupBy('user_id');
    }
}
