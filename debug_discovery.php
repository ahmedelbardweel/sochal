<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;
use App\Models\Post;
use App\Services\Post\DiscoveryService;

$discoveryService = app(DiscoveryService::class);

try {
    echo "Running getDiscoveryFeed...\n";
    $posts = $discoveryService->getDiscoveryFeed(5);
    echo "Count: " . count($posts->items()) . "\n";
    foreach ($posts->items() as $p) {
        echo "Post ID: {$p->id}, Hotness: {$p->hotness_score}, User: {$p->user->username}\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
