<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'is_admin' => true,
                'role' => AdminRole::SuperAdmin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'is_admin' => true,
                'role' => AdminRole::Admin,
            ]
        );

        User::updateOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor',
                'password' => 'password',
                'is_admin' => true,
                'role' => AdminRole::Editor,
            ]
        );
    }
}
