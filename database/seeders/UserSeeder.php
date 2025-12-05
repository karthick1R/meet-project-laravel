<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@meetingroom.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@meetingroom.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'bob@example.com'],
            [
                'name' => 'Bob Johnson',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
}
