<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'student', 'guard_name' => 'web']);
        Role::create(['name' => 'profesor', 'guard_name' => 'web']);
        Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
    }

    /**
     * Test that a student cannot access trainer routes
     */
    public function test_student_cannot_access_trainer_routes()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student, 'sanctum')->getJson('/api/v1/trainer/students');

        $response->assertStatus(403);
    }

    /**
     * Test that a student cannot create routines
     */
    public function test_student_cannot_create_routines()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student, 'sanctum')->postJson('/api/v1/trainer/routines', [
            'name' => 'Unauthorized Routine',
            'difficulty' => 'Básico',
            'exercises' => []
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test that a student cannot create categories
     */
    public function test_student_cannot_create_categories()
    {
        $student = User::factory()->create();
        $student->assignRole('student');

        $response = $this->actingAs($student, 'sanctum')->postJson('/api/v1/categories', [
            'name' => 'Unauthorized Category',
        ]);

        $response->assertStatus(403);
    }

    /**
     * Test that a trainer can access trainer routes
     */
    public function test_trainer_can_access_trainer_routes()
    {
        $trainer = User::factory()->create();
        $trainer->assignRole('profesor');

        $response = $this->actingAs($trainer, 'sanctum')->getJson('/api/v1/trainer/students');

        $response->assertStatus(200);
    }
}
