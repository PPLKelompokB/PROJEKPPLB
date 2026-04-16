<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('role', 'organizer')->first();

        Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Beach Clean Up Bali',
            'description' => 'Bersih pantai bersama',
            'location' => 'Pantai Kuta',
            'date' => now(),
            'duration' => 3,
            'quota' => 100
        ]);
    }
}