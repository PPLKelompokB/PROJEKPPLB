<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OrganizerSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Organizer 1',
            'email' => 'organizer@oceancare.com',
            'password' => Hash::make('123456'),
            'role' => 'organizer'
        ]);
    }
}
