<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventRegistration;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::first();

        if (!$event) {
            $this->command->warn('No event found.');
            return;
        }

        $registrations = EventRegistration::where('event_id', $event->id)->get();

        foreach ($registrations as $reg) {
            Attendance::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'user_id' => $reg->user_id,
                ],
                [
                    'status' => rand(0,1) ? 'present' : 'absent'
                ]
            );
        }
    }
}