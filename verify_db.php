<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "--- DATABASE VERIFICATION ---\n";
echo "Verified Users: " . App\Models\User::where('is_verified', true)->count() . "\n";
echo "Total Users: " . App\Models\User::count() . "\n";
echo "Total Posts: " . App\Models\Post::count() . "\n";
echo "Verified Posts: " . App\Models\Post::whereHas('user', function($q) { $q->where('is_verified', true); })->count() . "\n";

$u = App\Models\User::where('is_verified', true)->first();
if ($u) {
    echo "Sample User: {$u->username} (Verified: {$u->is_verified})\n";
    echo "Posts for {$u->username}:\n";
    foreach ($u->posts()->take(3)->get() as $p) {
        echo " - Post ID: {$p->id}, Status: {$p->status}, Privacy: {$p->privacy}, Views: {$p->views_count}\n";
    }
} else {
    echo "No verified users found.\n";
}

