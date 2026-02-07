<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LargeMovieStressSeeder extends Seeder
{
    public function run()
    {
        // 1. Create or Find Stress Tester User
        $user = User::firstOrCreate(
            ['email' => 'stress@tester.com'],
            [
                'username' => 'stress_tester_bot',
                'display_name' => 'Stress Tester',
                'password' => Hash::make('password'),
                'is_verified' => true,
                'avatar_url' => 'https://ui-avatars.com/api/?name=Stress+Tester&background=FF0000&color=fff',
            ]
        );

        $moviePath = asset('storage/posts/stress_test_movie.mp4');
        
        $this->command->info('Starting extreme stress test seeding (100 movies)...');

        for ($i = 1; $i <= 100; $i++) {
            $post = Post::create([
                'user_id' => $user->id,
                'caption' => "Stress Test Movie #$i: High Quality Encoding Simulation. Testing server throughput and database indexing for large high-bitrate files. #StressTest #Beekeeper #Performance",
                'type' => 'video',
                'privacy' => 'public',
                'status' => 'active',
                'likes_count' => rand(1000, 50000),
                'comments_count' => rand(500, 10000),
                'shares_count' => rand(100, 5000),
            ]);

            $post->media()->create([
                'type' => 'video',
                'url' => $moviePath,
                'thumbnail_url' => 'https://picsum.photos/seed/stress' . $i . '/1920/1080',
                'mime_type' => 'video/mp4',
                'duration' => 6300, // 1h 45m = 6300 seconds
                'status' => 'processed',
                'sort_order' => 0,
                'file_size' => 1433041590, // 1.4GB in bytes
            ]);

            if ($i % 10 === 0) {
                $this->command->info("Seeded $i / 100 movies...");
            }
        }

        $this->command->info('Extreme stress test seeding completed successfully!');
    }
}
