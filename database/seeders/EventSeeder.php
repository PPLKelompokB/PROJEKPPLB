<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('role', 'organizer')->first();

        if (!$organizer) {
            return;
        }

        $events = [
            [
                'organizer_id' => $organizer->id,
                'title' => 'Santa Monica Beach Clean-Up',
                'description' => 'Community cleanup event at Santa Monica Beach.',
                'location' => 'Santa Monica, CA',
                'event_date' => Carbon::create(2025, 3, 15, 9, 0, 0),
                'duration' => 3,
                'quota' => 100,
                'status' => 'published',
            ],
            [
                'organizer_id' => $organizer->id,
                'title' => 'Malibu Coastal Restoration',
                'description' => 'Restoring Malibu coastline and removing waste.',
                'location' => 'Malibu, CA',
                'event_date' => Carbon::create(2025, 3, 18, 8, 30, 0),
                'duration' => 2,
                'quota' => 80,
                'status' => 'published',
            ],
            [
                'organizer_id' => $organizer->id,
                'title' => 'Venice Beach Community Day',
                'description' => 'Community cleanup at Venice Beach.',
                'location' => 'Venice, CA',
                'event_date' => Carbon::create(2025, 3, 22, 10, 0, 0),
                'duration' => 4,
                'quota' => 120,
                'status' => 'published',
            ],
            [
                'organizer_id' => $organizer->id,
                'title' => 'Manhattan Beach Cleanup',
                'description' => 'Morning cleanup for marine conservation.',
                'location' => 'Manhattan Beach, CA',
                'event_date' => Carbon::create(2025, 3, 25, 7, 0, 0),
                'duration' => 2,
                'quota' => 60,
                'status' => 'published',
            ],
            [
                'organizer_id' => $organizer->id,
                'title' => 'Redondo Beach Restoration',
                'description' => 'Restoring beach after high tide debris.',
                'location' => 'Redondo Beach, CA',
                'event_date' => Carbon::create(2025, 3, 29, 9, 30, 0),
                'duration' => 3,
                'quota' => 90,
                'status' => 'published',
            ],
            [
                'organizer_id' => $organizer->id,
                'title' => 'Hermosa Beach Initiative',
                'description' => 'Monthly beach cleanup initiative.',
                'location' => 'Hermosa Beach, CA',
                'event_date' => Carbon::create(2025, 4, 2, 8, 0, 0),
                'duration' => 3,
                'quota' => 100,
                'status' => 'published',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}
