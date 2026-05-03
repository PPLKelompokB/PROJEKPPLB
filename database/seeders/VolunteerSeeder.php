<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class VolunteerSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(
                ['email' => "volunteer$i@mail.com"],
                [
                    'name' => "Volunteer $i",
                    'password' => Hash::make('123456'),
                    'role' => 'volunteer'
                ]
            );
        }
    }
}
