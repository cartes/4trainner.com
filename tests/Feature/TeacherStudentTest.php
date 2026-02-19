<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TeacherStudentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create roles if they don't exist (RefreshDatabase wipes them)
        if (!Role::where('name', 'profesor')->exists()) {
            Role::create(['name' => 'profesor', 'guard_name' => 'web']);
        }
        if (!Role::where('name', 'super-admin')->exists()) {
            Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        }
    }

    public function test_teacher_can_list_students()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('profesor');

        $student1 = User::factory()->create();
        $student2 = User::factory()->create();

        $teacher->students()->attach([$student1->id, $student2->id]);

        $response = $this->actingAs($teacher)->getJson('/api/v1/teacher/students');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_teacher_can_view_student_details()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('profesor');

        $student = User::factory()->create();
        $teacher->students()->attach($student->id);

        $response = $this->actingAs($teacher)->getJson('/api/v1/teacher/students/' . $student->id);

        $response->assertStatus(200)
            ->assertJson(['id' => $student->id]);
    }

    public function test_teacher_cannot_view_other_students()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('profesor');

        $otherStudent = User::factory()->create();
        // Not attached

        $response = $this->actingAs($teacher)->getJson('/api/v1/teacher/students/' . $otherStudent->id);

        $response->assertStatus(404);
    }

    public function test_teacher_can_view_students_list_page()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('profesor');

        $response = $this->actingAs($teacher)->get('/teacher/students');

        $response->assertStatus(200)
            ->assertSee('Gestión de Alumnos')
            ->assertSee('data-component="StudentIndex"', false);
    }

    public function test_teacher_can_view_student_details_page()
    {
        $teacher = User::factory()->create();
        $teacher->assignRole('profesor');

        $student = User::factory()->create();
        $teacher->students()->attach($student->id);

        $response = $this->actingAs($teacher)->get('/teacher/students/' . $student->id);

        $response->assertStatus(200)
            ->assertSee('Detalle de Alumno')
            ->assertSee('data-component="StudentShow"', false)
            ->assertSee('data-student-id="' . $student->id . '"', false);
    }
}
