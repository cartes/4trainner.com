<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Channel;
use App\Models\Video;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = Channel::all();

        foreach ($channels as $channel) {
            // Create 3-5 VOD videos for each channel
            Video::factory(rand(3, 5))->create([
                'channel_id' => $channel->id,
                'status' => 'vod',
            ]);

            // Randomly set a channel as having a 'live' video currently
            if (rand(1, 100) <= 30) { // 30% chance
                Video::factory()->create([
                    'channel_id' => $channel->id,
                    'status' => 'live',
                    'title' => 'Transmisión En Vivo: ' . $channel->name,
                    'duration' => null, // live doesn't have fixed duration yet
                    'file_path' => null, // live uses stream, not a static file path
                ]);
            }
        }
    }
}
