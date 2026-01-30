<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@supershop.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@supershop.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('Super Admin');
    }
}
