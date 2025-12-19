<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SUPERADMIN
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'), // ganti di server
                'role' => 'superadmin',
            ]
        );

        // 5 ADMIN BIASA
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "admin{$i}@example.com"],
                [
                    'name' => "Admin {$i}",
                    'password' => Hash::make('password123'), // ganti di server
                    'role' => 'admin',
                ]
            );
        }
    }
}