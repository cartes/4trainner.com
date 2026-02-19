<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $profesorRole = Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
        $alumnoRole = Role::firstOrCreate(['name' => 'alumno', 'guard_name' => 'web']);
        $moderadorRole = Role::firstOrCreate(['name' => 'moderador', 'guard_name' => 'web']);

        // Create 3 Professors
        User::factory(3)->create()->each(function ($trainer) use ($profesorRole, $alumnoRole) {
            $trainer->assignRole($profesorRole);

            // Create 5 Students for each Professor
            $students = User::factory(5)->create();
            foreach ($students as $student) {
                $student->assignRole($alumnoRole);
                // Link student to trainer using the pivot table
                $trainer->students()->attach($student->id);
            }
        });

        // Create 3 Moderators
        User::factory(3)->create()->each(function ($moderator) use ($moderadorRole) {
            $moderator->assignRole($moderadorRole);
        });
    }
}
