<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the SuperAdminSeeder
        $this->call(SuperAdminSeeder::class);

        // Crear el rol de Profesor si no existe
        $profesorRole = Role::firstOrCreate([
            'name' => 'profesor',
            'guard_name' => 'web',
        ]);

        // Crear el rol de Alumno si no existe
        $alumnoRole = Role::firstOrCreate([
            'name' => 'alumno',
            'guard_name' => 'web',
        ]);

        // Crear un usuario de prueba como Profesor si no existe ya
        if (!User::where('email', 'profesor@example.com')->exists()) {
            $profesor = User::create([
                'name' => 'Profesor Ejemplo',
                'email' => 'profesor@example.com',
                'password' => bcrypt('password'),
            ]);

            $profesor->assignRole($profesorRole);
        }

        // Crear un usuario de prueba como Alumno si no existe ya
        if (!User::where('email', 'alumno@example.com')->exists()) {
            $alumno = User::create([
                'name' => 'Alumno Ejemplo',
                'email' => 'alumno@example.com',
                'password' => bcrypt('password'),
            ]);

            $alumno->assignRole($alumnoRole);
        }
    }
}
