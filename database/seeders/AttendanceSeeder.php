<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Event;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $event = Event::first();
        $volunteers = User::where('role', 'volunteer')->take(3)->get();

        foreach ($volunteers as $volunteer) {
            Attendance::create([
                'event_id' => $event->id,
                'user_id' => $volunteer->id,
                'status' => 'hadir'
            ]);
        }
    }
}