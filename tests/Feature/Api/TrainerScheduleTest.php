<?php

namespace Tests\Feature\Api;

use App\Models\Channel;
use App\Models\ScheduledClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TrainerScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $trainer;
    private User $student;
    private Channel $channel;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['name' => 'profesor', 'guard_name' => 'web']);
        Role::create(['name' => 'alumno',   'guard_name' => 'web']);

        $this->trainer = User::factory()->create();
        $this->trainer->assignRole('profesor');

        $this->student = User::factory()->create();
        $this->student->assignRole('alumno');

        $this->channel = Channel::factory()->create(['user_id' => $this->trainer->id]);
        // Attach student to trainer via professor_student pivot
        $this->trainer->students()->attach($this->student->id);
    }

    public function test_trainer_can_create_scheduled_class(): void
    {
        $response = $this->actingAs($this->trainer)
            ->postJson('/api/v1/trainer/schedule', [
                'title'            => 'Yoga Matutino',
                'scheduled_at'     => now()->addDay()->toIso8601String(),
                'duration_minutes' => 60,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Yoga Matutino', 'status' => 'scheduled']);

        $this->assertDatabaseHas('scheduled_classes', [
            'title'      => 'Yoga Matutino',
            'channel_id' => $this->channel->id,
            'trainer_id' => $this->trainer->id,
        ]);
    }

    public function test_trainer_can_list_their_classes(): void
    {
        ScheduledClass::factory()->create([
            'channel_id' => $this->channel->id,
            'trainer_id' => $this->trainer->id,
            'scheduled_at' => now()->addHours(2),
        ]);

        $response = $this->actingAs($this->trainer)
            ->getJson('/api/v1/trainer/schedule');

        $response->assertOk()->assertJsonCount(1);
    }

    public function test_trainer_can_cancel_class(): void
    {
        $class = ScheduledClass::factory()->create([
            'channel_id' => $this->channel->id,
            'trainer_id' => $this->trainer->id,
            'scheduled_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->trainer)
            ->deleteJson("/api/v1/trainer/schedule/{$class->id}");

        $response->assertOk()->assertJsonFragment(['message' => 'Clase cancelada.']);
        $this->assertDatabaseHas('scheduled_classes', ['id' => $class->id, 'status' => 'cancelled']);
    }

    public function test_trainer_cannot_cancel_another_trainers_class(): void
    {
        $otherTrainer  = User::factory()->create();
        $otherTrainer->assignRole('profesor');
        $otherChannel  = Channel::factory()->create(['user_id' => $otherTrainer->id]);

        $class = ScheduledClass::factory()->create([
            'channel_id' => $otherChannel->id,
            'trainer_id' => $otherTrainer->id,
            'scheduled_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->trainer)
            ->deleteJson("/api/v1/trainer/schedule/{$class->id}");

        $response->assertForbidden();
    }

    public function test_student_sees_upcoming_classes_from_their_channels(): void
    {
        ScheduledClass::factory()->create([
            'channel_id'   => $this->channel->id,
            'trainer_id'   => $this->trainer->id,
            'scheduled_at' => now()->addHours(3),
            'status'       => 'scheduled',
        ]);

        $response = $this->actingAs($this->student)
            ->getJson('/api/v1/student/schedule');

        $response->assertOk()->assertJsonCount(1);
    }
}
