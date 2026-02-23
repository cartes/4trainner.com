<?php

namespace Tests\Unit\Models;

use App\Models\Routine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
        $this->assertEquals('Test User', $user->name);
    }

    public function test_user_can_have_students_and_trainers(): void
    {
        $professor = User::factory()->create();
        $student = User::factory()->create();

        // Attach student to professor via 'students' relationship
        $professor->students()->attach($student->id);

        // Refresh models to load relationships
        $professor->refresh();
        $student->refresh();

        // Assert professor has this student
        $this->assertTrue($professor->students->contains($student));

        // Assert student has this professor (via trainers relationship)
        $this->assertTrue($student->trainers->contains($professor));
    }

    public function test_user_can_have_assigned_routines(): void
    {
        $student = User::factory()->create();
        $trainer = User::factory()->create();

        // Create a routine manually since RoutineFactory might not exist or be configured
        $routine = Routine::create([
            'name' => 'Test Routine',
            'description' => 'A test routine description',
            'difficulty' => 'beginner',
            'trainer_id' => $trainer->id,
        ]);

        // Assign routine to student
        // The pivot table 'user_routine' likely needs 'assigned_by' field based on migration
        $student->assignedRoutines()->attach($routine->id, [
            'assigned_by' => $trainer->id,
            'is_active' => true
        ]);

        $student->refresh();

        $this->assertTrue($student->assignedRoutines->contains($routine));
        $this->assertEquals($trainer->id, $student->assignedRoutines->first()->pivot->assigned_by);
    }
}
