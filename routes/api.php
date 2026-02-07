<?php

use App\Http\Controllers\API\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Auth Routes (Session-Enabled for Web-Sync)
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::middleware([
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ])->group(function () {
            Route::post('register', 'register')->middleware('throttle:5,1');
            Route::post('verify-registration', 'verifyRegistration')->middleware('throttle:5,1');
            Route::post('resend-registration-otp', 'resendRegistrationOTP')->middleware('throttle:3,1');
            Route::post('login', 'login')->middleware('throttle:5,1');
        });
        
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('logout', 'logout');
            Route::get('me', 'me');
            
            // Security
            Route::post('verification/send', [\App\Http\Controllers\API\V1\SecurityController::class, 'sendVerification'])->middleware('throttle:3,1');
            Route::post('verification/verify', [\App\Http\Controllers\API\V1\SecurityController::class, 'verify'])->middleware('throttle:10,1');
        });

        // Password Recovery
        Route::post('password/request-reset', [\App\Http\Controllers\API\V1\SecurityController::class, 'requestReset'])->middleware('throttle:3,1');
        Route::post('password/reset', [\App\Http\Controllers\API\V1\SecurityController::class, 'resetPassword'])->middleware('throttle:5,1');
    });

    // Post Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('posts/discovery', [\App\Http\Controllers\API\V1\PostController::class, 'discovery']);
        Route::get('posts/following', [\App\Http\Controllers\API\V1\PostController::class, 'following']);
        Route::post('posts/{post}/view', [\App\Http\Controllers\API\V1\PostController::class, 'logView']);
        Route::apiResource('posts', \App\Http\Controllers\API\V1\PostController::class);
        Route::post('/test-upload', function(Request $request) {
            Log::info('Diagnostic Upload Request:', [
                'all' => $request->all(),
                'files' => array_keys($request->allFiles()),
            ]);
            return response()->json([
                'all' => $request->all(),
                'files' => array_keys($request->allFiles()),
                'has_media' => $request->has('media'),
                'has_image' => $request->has('image'),
            ]);
        });
        
        // Post Actions
        Route::patch('posts/{post}', [\App\Http\Controllers\API\V1\PostController::class, 'update']);
        Route::post('posts/{post}/hide', [\App\Http\Controllers\API\V1\PostController::class, 'hide']);
        Route::post('posts/{post}/unhide', [\App\Http\Controllers\API\V1\PostController::class, 'unhide']);

        // Social Interactions
        Route::post('posts/{post}/like', [\App\Http\Controllers\API\V1\InteractionController::class, 'toggleLike']);
        Route::post('posts/{post}/bookmark', [\App\Http\Controllers\API\V1\InteractionController::class, 'toggleBookmark']);
        
        // Comments
        Route::get('posts/{post}/comments', [\App\Http\Controllers\API\V1\CommentController::class, 'index']);
        Route::post('posts/{post}/comments', [\App\Http\Controllers\API\V1\CommentController::class, 'store']);
        Route::delete('comments/{comment}', [\App\Http\Controllers\API\V1\CommentController::class, 'destroy']);

        // Social Graph (Follows)
        Route::post('users/{user}/follow', [\App\Http\Controllers\API\V1\FollowController::class, 'follow']);
        Route::post('users/{user}/unfollow', [\App\Http\Controllers\API\V1\FollowController::class, 'unfollow']);
        Route::post('users/{user}/accept-request', [\App\Http\Controllers\API\V1\FollowController::class, 'accept']);
        Route::post('users/{user}/decline-request', [\App\Http\Controllers\API\V1\FollowController::class, 'decline']);
        Route::get('users/{user}/followers', [\App\Http\Controllers\API\V1\FollowController::class, 'followers']);
        Route::get('users/{user}/following', [\App\Http\Controllers\API\V1\FollowController::class, 'following']);
        Route::get('follow-requests', [\App\Http\Controllers\API\V1\FollowController::class, 'pendingRequests']);

        // Profile & Search
        Route::get('profile/{username}', [\App\Http\Controllers\API\V1\ProfileController::class, 'show']);
        Route::patch('profile', [\App\Http\Controllers\API\V1\ProfileController::class, 'update']);
        Route::post('profile/image', [\App\Http\Controllers\API\V1\ProfileController::class, 'uploadImage']);
        Route::get('profile/suggested', [\App\Http\Controllers\API\V1\ProfileController::class, 'suggested']);
        Route::get('search/users', [\App\Http\Controllers\API\V1\ProfileController::class, 'search']);

        // Stories
        Route::get('stories', [\App\Http\Controllers\API\V1\StoryController::class, 'index']);
        Route::post('stories', [\App\Http\Controllers\API\V1\StoryController::class, 'store']);
        Route::post('stories/{story}/view', [\App\Http\Controllers\API\V1\StoryController::class, 'markAsViewed']);

        // Live Sessions
        Route::prefix('live')->group(function () {
            Route::post('start', [\App\Http\Controllers\LiveController::class, 'start']);
            Route::post('feed', [\App\Http\Controllers\LiveController::class, 'feed']);
            Route::get('{id}', [\App\Http\Controllers\LiveController::class, 'show']);
            Route::post('{id}/join', [\App\Http\Controllers\LiveController::class, 'join']);
            Route::post('{id}/leave', [\App\Http\Controllers\LiveController::class, 'leave']);
            Route::post('{id}/end', [\App\Http\Controllers\LiveController::class, 'end']);
            Route::post('{id}/comment', [\App\Http\Controllers\LiveController::class, 'comment']);
            Route::post('{id}/react', [\App\Http\Controllers\LiveController::class, 'react']);
        });

        // Chats
        Route::get('chats', [\App\Http\Controllers\API\V1\ChatController::class, 'index']);
        Route::post('chats/direct/{user}', [\App\Http\Controllers\API\V1\ChatController::class, 'startDirectChat']);
        Route::get('chats/{chat}/messages', [\App\Http\Controllers\API\V1\ChatController::class, 'messages']);
        Route::post('chats/{chat}/messages', [\App\Http\Controllers\API\V1\ChatController::class, 'sendMessage']);
        Route::post('chats/{chat}/typing', [\App\Http\Controllers\API\V1\ChatController::class, 'sendTyping']);

        // Notifications
        Route::get('notifications', [\App\Http\Controllers\API\V1\NotificationController::class, 'index']);
        Route::post('notifications/{id}/read', [\App\Http\Controllers\API\V1\NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [\App\Http\Controllers\API\V1\NotificationController::class, 'markAllAsRead']);
        Route::delete('notifications/{id}', [\App\Http\Controllers\API\V1\NotificationController::class, 'destroy']);

        // Moderation
        Route::post('report', [\App\Http\Controllers\API\V1\ModerationController::class, 'report']);
        
        // Admin Routes (Could be gated further in middleware)
        Route::prefix('admin')->middleware('admin')->group(function () {
            Route::get('reports', [\App\Http\Controllers\API\V1\ModerationController::class, 'listReports']);
            Route::patch('reports/{id}', [\App\Http\Controllers\API\V1\ModerationController::class, 'resolveReport']);
            Route::get('stats', [\App\Http\Controllers\API\V1\ModerationController::class, 'getStats']);
        });
    });
});
