<?php
try {
    $currentUser = \App\Models\User::first();
    echo "Current User: " . ($currentUser->id ?? 'None') . "\n";
    
    $suggested = \App\Models\User::where('id', '!=', $currentUser?->id)
        ->withCount('followers')
        ->withSum('posts', 'views_count')
        ->limit(10)
        ->get();

    echo "Query Result Count: " . $suggested->count() . "\n";
    
    if ($suggested->isNotEmpty()) {
        $user = $suggested->first();
        echo "Sample User ID: " . $user->id . "\n";
        echo "Followers: " . $user->followers_count . "\n";
        echo "Views Sum: " . ($user->posts_sum_views_count ?? 'NULL') . "\n";
    }

    $result = $suggested->unique('id')->sortByDesc(function($user) {
        $score = ($user->is_verified ? 5000 : 0) + 
                 ($user->followers_count * 2) + 
                 ($user->posts_sum_views_count ?? 0);
        return $score;
    })->values();

    echo "Serialized Output:\n";
    echo json_encode(['data' => $result], JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
