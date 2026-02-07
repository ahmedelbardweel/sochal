<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

// Public/Landing Route
Route::get('/', function () {
    return auth()->check() ? redirect('/home') : redirect('/login');
});

// Guest Routes (Redirect to /home if already logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::get('/verify', function () { return view('auth.verify'); })->name('verify');
    Route::get('/password/reset', function () { return view('auth.reset-password'); })->name('password.reset');
    
    // Placeholders for auth processing
    Route::post('/login', function() { return back(); });
    Route::post('/register', function() { return back(); });
});

// Authenticated Routes (Redirect to /login if not logged in)
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home');
    })->name('home');

    Route::get('/profile/{username?}', function ($username = null) {
        return view('profile', ['username' => $username]);
    })->name('profile');

    Route::get('/explore', function () {
        // Get trending users (most followers + views)
        $suggestedUsers = \App\Models\User::where('id', '!=', auth()->id())
            ->withCount('followers')
            ->withSum('posts', 'views_count')
            ->orderBy('is_verified', 'desc')
            ->orderBy('followers_count', 'desc')
            ->orderBy('posts_sum_views_count', 'desc')
            ->limit(20)
            ->get()
            ->map(function($user) {
                $user->is_following = auth()->check() ? auth()->user()->isFollowing($user) : false;
                return $user;
            });
        
        return view('explore', ['suggestedUsers' => $suggestedUsers]);
    })->name('explore');

    Route::get('/create', function () {
        return view('create');
    })->name('create');

    Route::get('/reels', function () {
        return view('reels');
    })->name('reels');

    Route::get('/messages/{id?}', function ($id = null) {
        return view('messages', ['chatId' => $id]);
    })->name('messages');

    Route::get('/notifications', function () {
        return view('notifications');
    })->name('notifications');

    Route::get('/follow-requests', function () {
        return redirect()->route('notifications', ['tab' => 'requests']);
    })->name('follow-requests');

    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    // Admin Command Center
    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('admin.dashboard');
        Route::get('/reports', function () { return view('admin.reports'); })->name('admin.reports');
        Route::get('/users', function () { return view('admin.users'); })->name('admin.users');
    });
});
