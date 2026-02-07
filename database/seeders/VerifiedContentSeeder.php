<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Post;

class VerifiedContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $influencers = [
            [
                'username' => 'omari86',
                'display_name' => 'Omari Tech',
                'bio' => 'Building the future of finance 🚀 | Crypto Analyst & Investor',
                'seed' => 'omari',
            ],
            [
                'username' => 'lina_design',
                'display_name' => 'Lina Creative',
                'bio' => 'Minimalist UX/UI Designer based in Dubai 🎨✨',
                'seed' => 'lina',
            ],
            [
                'username' => 'chef_hassan',
                'display_name' => 'Chef Hassan',
                'bio' => 'Culinary adventures around the world 🌍🍳',
                'seed' => 'hassan',
            ],
            [
                'username' => 'dr_sarah',
                'display_name' => 'Dr. Sarah',
                'bio' => 'Health & Wellness Coach | MD 🩺🌿',
                'seed' => 'sarah',
            ],
            [
                'username' => 'travel_with_alex',
                'display_name' => 'Alex Explorer',
                'bio' => 'Capturing moments one click at a time 📸🏔️',
                'seed' => 'alex',
            ]
        ];

        foreach ($influencers as $data) {
            $user = User::updateOrCreate(
                ['username' => $data['username']],
                [
                    'display_name' => $data['display_name'],
                    'email' => $data['username'] . '@example.com',
                    'password' => Hash::make('password'),
                    'role' => 'creator',
                    'bio' => $data['bio'],
                    'is_verified' => true,
                    'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($data['display_name']) . '&background=random&size=200',
                ]
            );

            // Create 3-5 high quality posts for each
            $numPosts = rand(3, 5);
            for ($i = 0; $i < $numPosts; $i++) {
                $post = Post::create([
                    'user_id' => $user->id,
                    'caption' => 'Just sharing some amazing vibes from my latest project! #inspiration #life #aesthetic',
                    'privacy' => 'public',
                    'location' => 'Dream City, World',
                ]);

                // Attach media
                $post->media()->create([
                    'type' => 'image',
                    'url' => 'https://picsum.photos/seed/' . $data['seed'] . $i . '/800/800',
                    'mime_type' => 'image/jpeg',
                    'status' => 'processed',
                    'sort_order' => 0,
                ]);

                // Simulate views
                $post->views_count = rand(1500, 50000);
                $post->save();
            }
        }
    }
}
