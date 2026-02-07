<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an Admin/Dev user
        $admin = \App\Models\User::factory()->create([
            'username' => 'admin',
            'display_name' => 'AbsScroll Admin',
            'email' => 'admin@absscroll.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create 20 random users
        $users = \App\Models\User::factory(20)->create();

        // Each user creates 2-5 posts
        foreach ($users->concat([$admin]) as $user) {
            \App\Models\Post::factory(rand(2, 5))->create([
                'user_id' => $user->id,
            ])->each(function ($post) {
                // Add some media (dummy placeholders)
                $post->media()->create([
                    'type' => 'image',
                    'url' => 'https://picsum.photos/seed/' . $post->id . '/800/800',
                    'mime_type' => 'image/jpeg',
                    'status' => 'processed',
                    'sort_order' => 0,
                ]);
            });
        }
        // Call additional seeders
        $this->call([
            VerifiedContentSeeder::class,
        ]);
    }
}
