<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Post\FollowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FollowController extends Controller
{
    protected $followService;

    public function __construct(FollowService $followService)
    {
        $this->followService = $followService;
    }

    public function follow(Request $request, User $user)
    {
        try {
            \Illuminate\Support\Facades\Log::info("FOLLOW_API_CALL: Auth: " . $request->user()->username . " -> Target: " . $user->username);
            $result = $this->followService->follow($request->user(), $user);
            
            if (isset($result['error'])) {
                return response()->json(['message' => $result['error']], 422);
            }

            return response()->json([
                'message' => $result['status'] === 'pending' ? 'Follow request sent' : 'Following successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('FOLLOW_REQUEST_ERROR: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function unfollow(Request $request, User $user)
    {
        try {
            $result = $this->followService->unfollow($request->user(), $user);
            
            return response()->json([
                'message' => 'Unfollowed successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('UNFOLLOW_REQUEST_ERROR: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    public function accept(Request $request, User $user)
    {
        $result = $this->followService->acceptRequest($request->user(), $user);
        
        return response()->json([
            'message' => 'Request accepted',
            'data' => $result
        ]);
    }

    public function decline(Request $request, User $user)
    {
        $result = $this->followService->declineRequest($request->user(), $user);
        
        return response()->json([
            'message' => 'Request declined',
            'data' => $result
        ]);
    }

    public function followers(User $user)
    {
        $followers = $user->followers()->wherePivot('status', 'accepted')->paginate(20);
        return response()->json($followers);
    }

    public function following(User $user)
    {
        $following = $user->following()->wherePivot('status', 'accepted')->paginate(20);
        return response()->json($following);
    }

    /**
     * Get pending follow requests for the authenticated user
     */
    public function pendingRequests(Request $request)
    {
        $user = $request->user();
        \Illuminate\Support\Facades\Log::info("CHECKING_PENDING_REQUESTS: For user @{$user->username}");
        
        // Get all users who sent pending follow requests to this user
        $pendingRequests = $user->followers()
            ->wherePivot('status', 'pending')
            ->withPivot('created_at');
        
        $count = $pendingRequests->count();
        \Illuminate\Support\Facades\Log::info("PENDING_COUNT_FOUND: {$count}");
        
        $data = $pendingRequests->orderByPivot('created_at', 'desc')
            ->paginate(20);
        
        return response()->json([
            'data' => $data,
            'count' => $count
        ]);
    }
}
