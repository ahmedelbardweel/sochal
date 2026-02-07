<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\Post\InteractionService;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    protected $interactionService;

    public function __construct(InteractionService $interactionService)
    {
        $this->interactionService = $interactionService;
    }

    public function toggleLike(Request $request, Post $post)
    {
        $result = $this->interactionService->toggleLike($request->user(), $post);
        
        return response()->json([
            'message' => $result['liked'] ? 'Post liked' : 'Post unliked',
            'data' => $result
        ]);
    }

    public function toggleBookmark(Request $request, Post $post)
    {
        $result = $this->interactionService->toggleBookmark($request->user(), $post);
        
        return response()->json([
            'message' => $result['bookmarked'] ? 'Post bookmarked' : 'Bookmark removed',
            'data' => $result
        ]);
    }
}
