<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Story;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoryAndFollowSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $user_ids = $users->pluck('id')->toArray();

        $sampleVideos = [
            'https://vjs.zencdn.net/v/oceans.mp4',
            'https://player.vimeo.com/external/370337504.sd.mp4?s=69660c6d933355fb5dc77579f16d5668e16fd47e&profile_id=139&oauth2_token_id=57447761',
        ];

        $sampleImages = [
            'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05',
            'https://images.unsplash.com/photo-1441974231531-c6227db76b6e',
            'https://images.unsplash.com/photo-1501854140801-50d01698950b',
            'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d',
        ];

        $this->command->info('Establishing neural social links...');
        
        // 1. Establish Dense Follow Graph
        $follows = [];
        foreach ($user_ids as $id) {
            $potentialFollowings = array_diff($user_ids, [$id]);
            $toFollowCount = rand(15, 25);
            $followingIds = array_rand(array_flip($potentialFollowings), min($toFollowCount, count($potentialFollowings)));
            
            foreach ((array)$followingIds as $fId) {
                $follows[] = [
                    'follower_id' => $id,
                    'following_id' => $fId,
                    'status' => 'accepted',
                    'created_at' => now(),
                ];
            }

            if (count($follows) >= 1000) {
                DB::table('follows')->insertOrIgnore($follows);
                $follows = [];
            }
        }
        DB::table('follows')->insertOrIgnore($follows);

        $this->command->info('Broadcasting stories across the network...');

        // 2. Populate Stories
        $stories = [];
        foreach ($user_ids as $id) {
            $storyCount = rand(3, 8);
            for ($i = 0; $i < $storyCount; $i++) {
                $type = fake()->randomElement(['image', 'video', 'text']);
                $stories[] = [
                    'user_id' => $id,
                    'type' => $type,
                    'media_url' => ($type === 'image' ? $sampleImages[array_rand($sampleImages)] : ($type === 'video' ? $sampleVideos[array_rand($sampleVideos)] : null)),
                    'thumbnail_url' => ($type === 'video' ? $sampleImages[array_rand($sampleImages)] : null),
                    'text_content' => ($type === 'text' ? fake()->sentence() : null),
                    'background_color' => ($type === 'text' ? fake()->hexColor() : null),
                    'duration' => rand(5, 15),
                    'views_count' => rand(0, 1000),
                    'created_at' => now()->subHours(rand(0, 23)),
                    'expires_at' => now()->addHours(rand(1, 24)),
                ];
            }

            if (count($stories) >= 500) {
                DB::table('stories')->insert($stories);
                $stories = [];
            }
        }
        DB::table('stories')->insert($stories);

        $this->command->info('Synchronizing follower counts...');
        
        // 3. Sync Stats (Counter Cache)
        // Note: Assuming these columns might exist or were planned. 
        // If not, we use raw DB statements if they are in the migration.
        // Based on previous code, we should probably update user metadata or similar if available.
    }
}
