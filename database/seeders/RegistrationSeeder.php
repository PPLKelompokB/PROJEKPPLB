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

        if (!$event) {
            $this->command->warn('No event found.');
            return;
        }

        foreach ($volunteers as $volunteer) {
            EventRegistration::firstOrCreate([
                'event_id' => $event->id,
                'user_id' => $volunteer->id,
            ]);
        }
    }
}