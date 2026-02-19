<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the TestUserSeeder creates the correct number of users and roles.
     */
    public function test_user_seeder_creates_expected_data(): void
    {
        // Run the seeder
        $this->seed(TestUserSeeder::class);

        // Verify counts
        $this->assertEquals(3, User::role('profesor')->count());
        $this->assertEquals(15, User::role('alumno')->count());
        $this->assertEquals(3, User::role('moderador')->count());

        // Verify total users (not including potential default users created by seeder if any)
        // Based on the seeder logic: 3 (profesores) + 15 (alumnos) + 3 (moderadores) = 21
        $this->assertEquals(21, User::count());

        // Verify relationships
        $profesores = User::role('profesor')->get();
        foreach ($profesores as $profesor) {
            $this->assertEquals(5, $profesor->students()->count());
        }
    }
}
