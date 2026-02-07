<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GlobalStressTestSeeder extends Seeder
{
    public function run(): void
    {
        // Data clearing is handled by migrate:fresh

        // 2. Create Admin & Test Account
        $admin = User::create([
            'username' => 'admin',
            'email' => 'admin@upscrolling.com',
            'password' => Hash::make('password'),
            'display_name' => 'System Admin',
            'is_verified' => true,
        ]);

        // 3. Create ~500 Users
        $users = User::factory(500)->create()->push($admin);

        // 4. Sample Media URLs
        $sampleVideos = [
            'https://vjs.zencdn.net/v/oceans.mp4',
            'https://player.vimeo.com/external/370337504.sd.mp4?s=69660c6d933355fb5dc77579f16d5668e16fd47e&profile_id=139&oauth2_token_id=57447761',
            'https://player.vimeo.com/external/370337504.hd.mp4?s=0fc43a3d5483f982ea1295326778f63567d98344&profile_id=172&oauth2_token_id=57447761',
        ];

        $sampleImages = [
            'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05',
            'https://images.unsplash.com/photo-1441974231531-c6227db76b6e',
            'https://images.unsplash.com/photo-1501854140801-50d01698950b',
            'https://images.unsplash.com/photo-1447752875215-b2761acb3c5d',
            'https://images.unsplash.com/photo-1469474968028-56623f02e42e',
        ];

        // 5. Create 1000 Posts (Mixed Types)
        $posts = [];
        foreach ($users as $user) {
            $userPostCount = rand(5, 15);
            for ($i = 0; $i < $userPostCount; $i++) {
                $type = fake()->randomElement(['post', 'reel', 'video']);
                $post = Post::create([
                    'user_id' => $user->id,
                    'caption' => fake()->sentence() . ' #' . fake()->word() . ' #GlobalScale',
                    'type' => $type,
                    'status' => 'active',
                    'privacy' => 'public',
                ]);

                $mediaUrl = ($type === 'post') 
                    ? $sampleImages[array_rand($sampleImages)] . '?auto=format&fit=crop&w=800&q=80'
                    : $sampleVideos[array_rand($sampleVideos)];

                PostMedia::create([
                    'post_id' => $post->id,
                    'type' => ($type === 'post' ? 'image' : 'video'),
                    'url' => $mediaUrl,
                    'thumbnail_url' => $sampleImages[array_rand($sampleImages)] . '?auto=format&fit=crop&w=400&q=60',
                    'duration' => ($type === 'reel' ? rand(15, 59) : ($type === 'video' ? rand(181, 600) : null)),
                    'status' => 'processed',
                ]);

                $posts[] = $post;
            }
        }

        // 6. Create 3000 Random Follows
        $user_ids = $users->pluck('id')->toArray();
        $follows = [];
        for ($i = 0; $i < 3000; $i++) {
            $followerId = $user_ids[array_rand($user_ids)];
            $followingId = $user_ids[array_rand($user_ids)];
            if ($followerId !== $followingId) {
                $follows[] = [
                    'follower_id' => $followerId,
                    'following_id' => $followingId,
                    'created_at' => now(),
                ];
            }
        }
        DB::table('follows')->insertOrIgnore($follows);

        // 7. Create 5000 Random Likes
        $likes = [];
        foreach (array_rand($posts, 500) as $postIdx) {
            $p = $posts[$postIdx];
            $liker_ids = array_rand(array_flip($user_ids), rand(5, 20));
            foreach ($liker_ids as $uid) {
                $likes[] = [
                    'user_id' => $uid,
                    'post_id' => $p->id,
                    'created_at' => now(),
                ];
            }
        }
        DB::table('likes')->insertOrIgnore($likes);

        // 8. Create 10,000 Random Views
        $views = [];
        for ($i = 0; $i < 10000; $i++) {
            $views[] = [
                'post_id' => $posts[array_rand($posts)]->id,
                'user_id' => fake()->boolean(70) ? $user_ids[array_rand($user_ids)] : null,
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'created_at' => now()->subMinutes(rand(0, 10000)),
            ];
            if (count($views) >= 1000) {
                DB::table('post_views')->insert($views);
                $views = [];
            }
        }

        // 9. Sync Counts (Counter Cache)
        DB::statement('UPDATE posts SET likes_count = (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id)');
        DB::statement('UPDATE posts SET comments_count = (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id)');
        DB::statement('UPDATE posts SET views_count = (SELECT COUNT(*) FROM post_views WHERE post_views.post_id = posts.id)');
    }
}
