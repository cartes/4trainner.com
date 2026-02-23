<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'cover_image' => 'https://picsum.photos/seed/' . $this->faker->uuid() . '/800/450',
            // user_id MUST be overridden when calling the factory or handled generally.
            'stream_key' => 'live_4train_' . Str::random(16),
            'is_live' => $this->faker->boolean(20), // 20% chance of being live
        ];
    }
}
