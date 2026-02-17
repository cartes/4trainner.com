<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create the super-admin role
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        // Create or update the super-admin user
        $user = User::updateOrCreate(
            ['email' => 'cristiancartesa@gmail.com'],
            [
                'name' => 'Cristian Cartesa',
                'password' => Hash::make('Front_242'), // Using the password found in existing seeder
                'email_verified_at' => now(),
            ]
        );

        // Assign the role to the user
        if (!$user->hasRole('super-admin')) {
            $user->assignRole($role);
        }
    }
}
