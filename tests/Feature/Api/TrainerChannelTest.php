<?php

namespace Tests\Feature\Api;

use App\Models\Channel;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrainerChannelTest extends TestCase
{
    use RefreshDatabase;

    private User $trainer;
    private Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'profesor', 'guard_name' => 'web']);
        Role::create(['name' => 'alumno', 'guard_name' => 'web']);

        $this->trainer = User::factory()->create();
        $this->trainer->assignRole('profesor');

        $this->channel = Channel::create([
            'user_id'     => $this->trainer->id,
            'name'        => 'Canal de Prueba',
            'description' => 'Canal para tests',
        ]);
    }

    public function test_trainer_can_see_own_channel(): void
    {
        $this->actingAs($this->trainer)
            ->getJson('/api/v1/trainer/channel')
            ->assertOk()
            ->assertJsonPath('channel.id', $this->channel->id)
            ->assertJsonStructure(['channel' => ['id', 'name', 'stream_key', 'is_live', 'rtmp_url', 'videos']]);
    }

    public function test_trainer_without_channel_gets_404(): void
    {
        $trainer2 = User::factory()->create();
        $trainer2->assignRole('profesor');

        $this->actingAs($trainer2)
            ->getJson('/api/v1/trainer/channel')
            ->assertNotFound();
    }

    public function test_status_polling_returns_live_state(): void
    {
        $this->channel->update(['is_live' => false]);

        $this->actingAs($this->trainer)
            ->getJson('/api/v1/trainer/channel/status')
            ->assertOk()
            ->assertJson(['is_live' => false, 'video_count' => 0]);

        $this->channel->update(['is_live' => true]);

        $this->actingAs($this->trainer)
            ->getJson('/api/v1/trainer/channel/status')
            ->assertOk()
            ->assertJson(['is_live' => true]);
    }

    public function test_trainer_can_upload_vod_video(): void
    {
        Storage::fake('public');

        $this->actingAs($this->trainer)
            ->postJson('/api/v1/trainer/channel/videos', [
                'title'       => 'Clase de Yoga',
                'description' => 'Sesión completa',
                'video'       => UploadedFile::fake()->create('yoga.mp4', 1024, 'video/mp4'),
            ])
            ->assertCreated()
            ->assertJsonPath('video.title', 'Clase de Yoga')
            ->assertJsonPath('video.status', 'vod');

        $this->assertDatabaseHas('videos', [
            'channel_id' => $this->channel->id,
            'title'      => 'Clase de Yoga',
        ]);
    }

    public function test_upload_fails_without_title(): void
    {
        Storage::fake('public');

        $this->actingAs($this->trainer)
            ->postJson('/api/v1/trainer/channel/videos', [
                'video' => UploadedFile::fake()->create('yoga.mp4', 512, 'video/mp4'),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_trainer_cannot_delete_video_from_another_channel(): void
    {
        $otherTrainer = User::factory()->create();
        $otherTrainer->assignRole('profesor');
        $otherChannel = Channel::create([
            'user_id' => $otherTrainer->id,
            'name'    => 'Otro Canal',
        ]);
        $otherVideo = Video::create([
            'channel_id' => $otherChannel->id,
            'title'      => 'Video Ajeno',
            'status'     => 'vod',
        ]);

        $this->actingAs($this->trainer)
            ->deleteJson("/api/v1/trainer/channel/videos/{$otherVideo->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('videos', ['id' => $otherVideo->id]);
    }

    public function test_trainer_can_delete_own_video(): void
    {
        Storage::fake('public');

        $video = Video::create([
            'channel_id' => $this->channel->id,
            'title'      => 'Mi Video',
            'status'     => 'vod',
            'file_path'  => 'videos/channel-1/test.mp4',
        ]);

        $this->actingAs($this->trainer)
            ->deleteJson("/api/v1/trainer/channel/videos/{$video->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Video eliminado correctamente.');

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_student_cannot_access_trainer_channel_endpoints(): void
    {
        $student = User::factory()->create();
        $student->assignRole('alumno');

        $this->actingAs($student)
            ->getJson('/api/v1/trainer/channel')
            ->assertForbidden();
    }
}
