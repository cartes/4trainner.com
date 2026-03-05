<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduledClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'channel_id'       => Channel::factory(),
            'trainer_id'       => User::factory(),
            'title'            => $this->faker->sentence(3),
            'description'      => $this->faker->optional()->sentence(),
            'scheduled_at'     => $this->faker->dateTimeBetween('+1 hour', '+30 days'),
            'duration_minutes' => $this->faker->randomElement([30, 45, 60, 90]),
            'max_students'     => $this->faker->optional()->numberBetween(5, 50),
            'status'           => 'scheduled',
        ];
    }
}
