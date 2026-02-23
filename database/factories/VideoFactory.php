<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_id' => \App\Models\Channel::factory(),
            'title' => $this->faker->sentence(6),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['live', 'vod', 'processing']),
            'file_path' => 'sample.mp4',
            'thumbnail_path' => 'https://picsum.photos/seed/' . $this->faker->uuid() . '/1280/720',
            'duration' => $this->faker->time('H:i:s'),
        ];
    }
}
