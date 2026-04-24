<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventRegistration;
use App\Models\User;
use App\Models\Event;

class RegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::first();
        $volunteers = User::where('role', 'volunteer')->get();

        foreach ($volunteers as $volunteer) {
            EventRegistration::create([
                'event_id' => $event->id,
                'user_id' => $volunteer->id,
                'status' => 'registered'
            ]);
        }
    }
}