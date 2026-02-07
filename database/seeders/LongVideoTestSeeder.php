<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Hash;

class LongVideoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a special user for these videos
        $user = User::where('username', 'video_bot')->first();
        
        if (!$user) {
            $user = User::create([
                'username' => 'video_bot',
                'display_name' => 'Long Content Bot 🎥',
                'email' => 'bot@absscroll.com',
                'password' => Hash::make('password'),
                'role' => 'creator',
                'is_verified' => true,
                'avatar_url' => 'https://ui-avatars.com/api/?name=Video+Bot&background=FF0000&color=fff',
                'bio' => 'Automated bot for testing long-form video content stability.'
            ]);
        }

        // Use one of the existing video files as a template to save space
        // We found this file earlier in storage/app/public/posts/
        $videoPath = 'posts/00f5511a-575e-4f0a-8cbb-93c80d6710c6.mp4';
        
        $this->command->info("Starting to seed 50 videos (40 minutes each)...");

        for ($i = 1; $i <= 50; $i++) {
            $post = Post::create([
                'user_id' => $user->id,
                'caption' => "Test Video #{$i}: Deep Dive into System Architecture (Part 1). This is a simulated 40-minute session. #QualityContent #LongForm #Tech",
                'privacy' => 'public',
                'type' => 'video',
                'status' => 'active',
                'views_count' => rand(1000, 100000),
                'likes_count' => rand(50, 5000),
                'comments_count' => rand(10, 800),
                'location' => 'Silicon Valley, CA',
            ]);

            $post->media()->create([
                'type' => 'video',
                'url' => $videoPath,
                'thumbnail_url' => 'https://picsum.photos/seed/thumb' . $i . '/1280/720',
                'mime_type' => 'video/mp4',
                'duration' => 2400, // 40 minutes = 2400 seconds
                'status' => 'processed',
                'sort_order' => 0,
                'file_size' => 45740441, // Approx size of the template file in bytes
            ]);

            if ($i % 10 == 0) {
                $this->command->info("Seeded {$i} videos...");
            }
        }
        
        $this->command->info("Success! 50 videos have been added to the discovery feed.");
    }
}
