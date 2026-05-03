<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@oceancare.com'],
            [
                'name' => 'Admin OceanCare',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '08123456789',
            ]
        );
    }
}