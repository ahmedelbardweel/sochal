<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\PostMedia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VideoPostsSeeder extends Seeder
{
    public function run(): void
    {
        $sourceVideo = 'C:\Users\Ahmed\Videos\The.Beekeeper.2024.1080p.WEB-DL.EgyDead.CoM.mp4';
        
        // Check if source video exists
        if (!file_exists($sourceVideo)) {
            $this->command->error("Source video not found: {$sourceVideo}");
            return;
        }

        $this->command->info("Starting video posts seeding...");
        
        // Ensure storage directories exist
        $videosPath = storage_path('app/public/videos');
        if (!File::exists($videosPath)) {
            File::makeDirectory($videosPath, 0755, true);
        }

        // Copy video to storage (single copy to save space)
        $videoFilename = 'beekeeper_2024.mp4';
        $destinationPath = $videosPath . '/' . $videoFilename;
        
        if (!file_exists($destinationPath)) {
            $this->command->info("Copying video file...");
            copy($sourceVideo, $destinationPath);
            $this->command->info("Video copied successfully!");
        } else {
            $this->command->info("Video already exists in storage.");
        }

        // Get all users
        $users = User::all();
        $totalUsers = $users->count();
        
        if ($totalUsers === 0) {
            $this->command->error("No users found in database!");
            return;
        }

        $this->command->info("Found {$totalUsers} users. Creating 50 posts for each...");

        $captions = [
            "🎬 Just watched this masterpiece! Amazing action scenes! 🔥",
            "Can't stop watching this! The story is incredible 💯",
            "This movie is pure gold! Must watch! ⭐⭐⭐⭐⭐",
            "The best action film I've seen this year! 🎯",
            "Absolutely mind-blowing! Can't recommend enough! 🚀",
            "This is what cinema is all about! 🎥",
            "Pure entertainment from start to finish! 🍿",
            "The action sequences are insane! 💥",
            "A masterclass in filmmaking! 👏",
            "This movie deserves all the awards! 🏆",
        ];

        $bar = $this->command->getOutput()->createProgressBar($totalUsers * 50);
        $bar->start();

        foreach ($users as $user) {
            for ($i = 1; $i <= 50; $i++) {
                // Create post
                $post = Post::create([
                    'user_id' => $user->id,
                    'type' => 'reel',
                    'caption' => $captions[array_rand($captions)] . " #" . $i,
                    'privacy' => 'public',
                    'comments_disabled' => false,
                    'created_at' => now()->subMinutes(rand(1, 10080)), // Random time in last week
                ]);

                // Create media for post
                PostMedia::create([
                    'post_id' => $post->id,
                    'type' => 'video',
                    'url' => 'videos/' . $videoFilename,
                    'thumbnail_url' => null,
                    'width' => 1920,
                    'height' => 1080,
                    'duration' => 105 * 60, // Movie duration in seconds (105 minutes)
                    'file_size' => filesize($destinationPath),
                    'mime_type' => 'video/mp4',
                    'sort_order' => 1,
                    'status' => 'processed',
                ]);

                // Add random views
                $post->update([
                    'views_count' => rand(100, 10000)
                ]);

                $bar->advance();
            }
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("✅ Successfully created " . ($totalUsers * 50) . " video posts!");
    }
}
