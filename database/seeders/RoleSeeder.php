<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Roles
        $roles = [
            'Super Admin',
            'Store Manager',
            'Accountant',
            'Cashier',
            'Inventory Manager',
            'Sales Staff',
            'Customer' // Added Customer role for frontend users
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        // Example: Create default super admin user (optional, can be done in a separate seeder)
        // $user = \App\Models\User::factory()->create([
        //     'name' => 'Super Admin',
        //     'email' => 'admin@supershop.com',
        //     'password' => bcrypt('password'),
        // ]);
        // $user->assignRole('Super Admin');
    }
}
