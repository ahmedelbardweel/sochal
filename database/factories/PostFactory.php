<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'caption' => fake()->paragraph() . ' #AbsScroll #NewPost',
            'privacy' => 'public',
            'likes_count' => fake()->numberBetween(10, 500),
            'comments_count' => fake()->numberBetween(5, 50),
            'status' => 'active',
        ];
    }
}
