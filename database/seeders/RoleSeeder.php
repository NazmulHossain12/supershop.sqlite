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

        // 1. Define Permissions
        $permissions = [
            'view_reports',
            'manage_inventory',
            'access_pos',
            'delete_transactions',
            'manage_users',
            'manage_roles',
            'void_invoices',
            'view_all_data',
            'view_own_data',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Define Roles and Assign Permissions
        $roles = [
            'Super Admin' => $permissions, // God Mode
            'Admin' => [
                'view_reports',
                'manage_inventory',
                'access_pos',
                'manage_users',
                'view_all_data',
                'view_own_data',
            ],
            'Manager' => [
                'access_pos',
                'void_invoices',
                'view_reports',
                'view_all_data',
                'manage_inventory',
                'view_own_data',
            ],
            'Accountant' => [
                'view_reports',
                'view_all_data',
                'view_own_data',
            ],
            'Storekeeper' => [
                'manage_inventory',
                'view_all_data', // To see what they are inventorying
                'view_own_data',
            ],
            'Cashier' => [
                'access_pos',
                'view_own_data',
            ],
            'Auditor' => [
                'view_all_data',
                'view_reports',
                'view_own_data',
            ],
            'Customer' => [
                'view_own_data',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        // 3. Assign Super Admin to first user
        $admin = \App\Models\User::first();
        if ($admin) {
            $admin->assignRole('Super Admin');
        }
    }
}
