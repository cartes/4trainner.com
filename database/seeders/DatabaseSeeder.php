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
        // Crear el rol de Super Admin si no existe
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web', // Añadir el guard_name explícitamente
        ]);

        // Crear un usuario y asignarle el rol si no existe ya
        if (!User::where('email', 'cristiancartesa@gmail.com')->exists()) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'cristiancartesa@gmail.com',
                'password' => bcrypt('Front_242'),
            ]);

            $user->assignRole($superAdminRole);
        }
    }
}
