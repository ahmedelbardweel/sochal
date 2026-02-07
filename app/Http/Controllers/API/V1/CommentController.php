<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Services\Post\InteractionService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $interactionService;

    public function __construct(InteractionService $interactionService)
    {
        $this->interactionService = $interactionService;
    }

    public function index(Post $post)
    {
        $comments = $post->comments()->with(['user', 'replies.user'])->latest()->paginate(20);
        return response()->json($comments);
    }

    public function store(Request $request, Post $post)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = $this->interactionService->addComment(
            $request->user(),
            $post,
            $request->only(['comment', 'parent_id'])
        );

        return response()->json([
            'message' => 'Comment added successfully',
            'data' => $comment
        ], 201);
    }

    public function destroy(Request $request, Comment $comment)
    {
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();
        $comment->post()->decrement('comments_count');

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }
}
