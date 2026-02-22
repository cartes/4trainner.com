<?php

namespace Tests\Feature;

use App\Models\Routine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class StudentDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_dashboard_returns_assigned_routines_only(): void
    {
        // Setup Roles
        if (!Role::where('name', 'alumno')->exists()) {
            Role::create(['name' => 'alumno', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'profesor')->exists()) {
            Role::create(['name' => 'profesor', 'guard_name' => 'web']);
        }

        $student = User::factory()->create();
        $student->assignRole('alumno');

        $trainer = User::factory()->create();
        $trainer->assignRole('profesor');

        // Create routines manually
        $routineAssigned = Routine::create([
            'name' => 'Assigned Routine',
            'description' => 'Test',
            'difficulty' => 'Básico',
            'trainer_id' => $trainer->id,
        ]);

        $routineNotAssigned = Routine::create([
            'name' => 'Not Assigned Routine',
            'description' => 'Test',
            'difficulty' => 'Avanzado',
            'trainer_id' => $trainer->id,
        ]);

        // Assign one routine to student via user_routine pivot
        // Columns: user_id, routine_id, assigned_by, is_active
        $student->assignedRoutines()->attach($routineAssigned->id, [
            'assigned_by' => $trainer->id,
            'is_active' => true,
        ]);

        // Assign trainer to student via professor_student pivot
        $student->trainers()->attach($trainer->id);

        $response = $this->actingAs($student)
            ->getJson('/api/v1/student/dashboard');

        $response->assertOk();

        $routines = $response->json('routines');
        $this->assertCount(1, $routines);
        $this->assertEquals($routineAssigned->id, $routines[0]['id']);
    }
}
