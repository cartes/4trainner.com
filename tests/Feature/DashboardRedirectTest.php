<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestUserSeeder::class);
    }

    public function test_profesor_is_redirected_to_trainer_dashboard(): void
    {
        $profesor = User::role('profesor')->first();

        $response = $this->actingAs($profesor)->get('/dashboard');

        $response->assertRedirect('/trainer/dashboard');
    }

    public function test_alumno_is_redirected_to_student_dashboard(): void
    {
        $alumno = User::role('alumno')->first();

        $response = $this->actingAs($alumno)->get('/dashboard');

        $response->assertRedirect('/student/dashboard');
    }

    public function test_moderador_is_redirected_to_moderator_dashboard(): void
    {
        $moderador = User::role('moderador')->first();

        $response = $this->actingAs($moderador)->get('/dashboard');

        $response->assertRedirect('/moderator/dashboard');
    }

    public function test_trainer_dashboard_requires_auth(): void
    {
        $response = $this->get('/trainer/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_student_dashboard_requires_auth(): void
    {
        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_moderator_dashboard_requires_auth(): void
    {
        $response = $this->get('/moderator/dashboard');
        $response->assertRedirect('/login');
    }
}
