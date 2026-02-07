<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;

class ModerationController extends Controller
{
    /**
     * Submit a report (User action)
     */
    public function report(Request $request)
    {
        $request->validate([
            'type' => 'required|in:post,user,comment',
            'target_id' => 'required|integer',
            'reason' => 'required|string|max:100',
            'details' => 'nullable|string|max:1000',
        ]);

        $types = [
            'post' => Post::class,
            'user' => User::class,
            'comment' => Comment::class,
        ];

        $report = Report::create([
            'reporter_id' => $request->user()->id,
            'reportable_type' => $types[$request->type],
            'reportable_id' => $request->target_id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        return response()->json([
            'message' => 'Report submitted successfully. Thank you for keeping our community safe.',
            'data' => $report
        ], 201);
    }

    /**
     * Admin: List reports
     */
    public function listReports(Request $request)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'moderator') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reports = Report::with(['reporter', 'reportable'])->latest()->paginate(50);
        return response()->json($reports);
    }

    /**
     * Admin: Resolve a report
     */
    public function resolveReport(Request $request, $id)
    {
        if ($request->user()->role !== 'admin' && $request->user()->role !== 'moderator') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:warn,delete,suspend,dismiss',
            'notes' => 'nullable|string'
        ]);

        $report = Report::findOrFail($id);
        
        // Take specific action based on type
        if ($request->action === 'delete') {
            $report->reportable()->delete(); // Soft delete the content
        } elseif ($request->action === 'suspend') {
            $user = ($report->reportable_type === User::class) 
                ? $report->reportable 
                : $report->reportable->user;
            
            if ($user) {
                $user->update(['status' => 'suspended']);
            }
        }

        $report->update([
            'status' => 'resolved',
            'moderator_id' => $request->user()->id,
            'moderation_notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Report resolved with action: ' . $request->action,
            'report' => $report
        ]);
    }

    /**
     * Admin: Get stats
     */
    public function getStats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'users' => User::count(),
            'posts' => Post::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
            'reports_today' => Report::whereDate('created_at', now()->today())->count(),
        ]);
    }
}
