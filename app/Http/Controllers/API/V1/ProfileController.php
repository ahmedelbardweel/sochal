<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get profile by username
     */
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $currentUser = request()->user();
        
        $userData = $user->loadCount(['posts', 'followers', 'following']);
        $userData->is_following = $currentUser ? $currentUser->isFollowing($user) : false;
        $userData->has_pending_request = $currentUser ? $currentUser->hasPendingFollowRequest($user) : false;

        return response()->json([
            'user' => $userData
        ]);
    }

    /**
     * Update authenticated user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'display_name' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'location' => 'nullable|string|max:100',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'is_private' => 'nullable|boolean',
        ]);

        if (array_key_exists('is_private', $data)) {
            $data['is_private'] = filter_var($data['is_private'], FILTER_VALIDATE_BOOLEAN);
        }

        \Illuminate\Support\Facades\Log::info("PROFILE_UPDATE: ID {$user->id}", $data);
        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Upload avatar or cover
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'type' => 'required|in:avatar,cover',
            'image' => 'required|image|max:5120', // 5MB
        ]);

        $user = $request->user();
        $type = $request->type;
        $field = $type . '_url';

        // Delete old if exists
        if ($user->$field) {
            $oldPath = str_replace(url('/storage'), '', $user->$field);
            Storage::disk('public')->delete($oldPath);
        }

        $path = $request->file('image')->store('profiles/' . $type, 'public');
        // Store just the path, the User model accessor will handle URL generation
        $user->update([$field => $path]);

        return response()->json([
            'message' => ucfirst($type) . ' uploaded successfully',
            'url' => asset('storage/' . $path)
        ]);
    }

    /**
     * Search users
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        $currentUser = $request->user();
        
        if (!$query) {
            return response()->json(['data' => []]);
        }

        $users = User::where('id', '!=', $currentUser?->id)
            ->where(function($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                  ->orWhere('display_name', 'LIKE', "%{$query}%");
            })
            ->withCount(['followers', 'posts'])
            ->withExists(['followers as is_following' => function($q) use ($currentUser) {
                $q->where('follower_id', $currentUser?->id)->where('follows.status', 'accepted');
            }])
            ->withExists(['followers as has_pending_request' => function($q) use ($currentUser) {
                $q->where('follower_id', $currentUser?->id)->where('follows.status', 'pending');
            }])
            ->paginate(20);

        return response()->json($users);
    }

    /**
     * Get suggested users for discovery
     */
    public function suggested(Request $request)
    {
        $currentUser = $request->user();
        $cacheKey = "suggested_users_v3_" . ($currentUser?->id ?? 'guest');

        $users = \Cache::remember($cacheKey, now()->addMinutes(10), function() use ($currentUser) {
            // Priority: Users with high engagement (Followers + Total Post Views)
            $suggested = User::where('id', '!=', $currentUser?->id)
                ->whereDoesntHave('followers', function($query) use ($currentUser) {
                    $query->where('follower_id', $currentUser?->id);
                })
                ->withCount('followers')
                ->withSum('posts', 'views_count') // Calculate total views
                ->withExists(['followers as is_following' => function($q) use ($currentUser) {
                    $q->where('follower_id', $currentUser?->id)->where('follows.status', 'accepted');
                }])
                ->withExists(['followers as has_pending_request' => function($q) use ($currentUser) {
                    $q->where('follower_id', $currentUser?->id)->where('follows.status', 'pending');
                }])
                ->orderBy('is_verified', 'desc')
                ->orderBy('followers_count', 'desc')
                ->orderBy('posts_sum_views_count', 'desc')
                ->limit(60)
                ->get();

            // Fallback: If not enough, fill with global top creators regardless of follow status (for discovery)
            if ($suggested->count() < 20) {
                $excludedIds = $suggested->pluck('id')->push($currentUser?->id)->filter();
                $topGlobal = User::whereNotIn('id', $excludedIds)
                    ->withCount('followers')
                    ->withSum('posts', 'views_count')
                    ->withExists(['followers as is_following' => function($q) use ($currentUser) {
                        $q->where('follower_id', $currentUser?->id)->where('follows.status', 'accepted');
                    }])
                    ->withExists(['followers as has_pending_request' => function($q) use ($currentUser) {
                        $q->where('follower_id', $currentUser?->id)->where('follows.status', 'pending');
                    }])
                    ->orderBy('followers_count', 'desc')
                    ->orderBy('posts_sum_views_count', 'desc')
                    ->limit(60 - $suggested->count())
                    ->get();
                
                $suggested = $suggested->merge($topGlobal);
            }

            // Final Polish: Sort by "Trending Score" (Followers + Views)
            return $suggested->unique('id')->sortByDesc(function($user) {
                $score = ($user->is_verified ? 5000 : 0) + 
                         ($user->followers_count * 2) + 
                         ($user->posts_sum_views_count ?? 0);
                return $score;
            })->values();
        });

        return response()->json([
            'data' => $users
        ]);
    }
}
