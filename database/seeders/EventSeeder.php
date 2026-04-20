<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Santa Monica Beach Clean-Up',
                'description' => "Join us for a meaningful morning cleaning up Santa Monica Beach! This community-driven event aims to remove plastic waste, debris, and litter from our beautiful coastline while raising awareness about marine conservation.\n\nWe'll provide all necessary equipment including gloves, trash bags, and pickup tools. Light refreshments will be available after the cleanup. This is a family-friendly event suitable for all ages.\n\nWhat to Bring:\n• Comfortable walking shoes\n• Sun protection (hat, sunscreen)\n• Water bottle\n• Positive attitude!",
                'location' => 'Santa Monica, CA', // Meeting Point: Santa Monica Pier Entrance
                'event_date' => Carbon::create(2025, 3, 15, 9, 0, 0), // 15 Maret 2025, 09:00 AM
                'status' => 'published',
            ],
            [
                'title' => 'Malibu Coastal Restoration',
                'description' => 'Join Coastal Protectors for a morning of restoring the beautiful Malibu coastline. We will be focusing on removing microplastics and protecting local marine habitats.',
                'location' => 'Malibu, CA',
                'event_date' => Carbon::create(2025, 3, 18, 8, 30, 0), // 18 Maret 2025, 08:30 AM
                'status' => 'published',
            ],
            [
                'title' => 'Venice Beach Community Day',
                'description' => 'A community day organized by Beach Warriors to keep Venice Beach clean and safe for everyone. Equipment provided, just bring your energy!',
                'location' => 'Venice, CA',
                'event_date' => Carbon::create(2025, 3, 22, 10, 0, 0), // 22 Maret 2025, 10:00 AM
                'status' => 'published',
            ],
            [
                'title' => 'Manhattan Beach Cleanup',
                'description' => 'Early morning cleanup session organized by Marine Life Savers at Manhattan Beach. Great opportunity to earn volunteer hours while saving marine life.',
                'location' => 'Manhattan Beach, CA',
                'event_date' => Carbon::create(2025, 3, 25, 7, 0, 0), // 25 Maret 2025, 07:00 AM
                'status' => 'published',
            ],
            [
                'title' => 'Redondo Beach Restoration',
                'description' => 'Eco Warriors invite you to help restore Redondo Beach\'s natural beauty. We will be clearing the shoreline of debris left by recent high tides.',
                'location' => 'Redondo Beach, CA',
                'event_date' => Carbon::create(2025, 3, 29, 9, 30, 0), // 29 Maret 2025, 09:30 AM
                'status' => 'published',
            ],
            [
                'title' => 'Hermosa Beach Initiative',
                'description' => 'Clean Coast Collective\'s monthly initiative to clear plastics and debris from Hermosa Beach. Open to individuals and corporate volunteer groups.',
                'location' => 'Hermosa Beach, CA',
                'event_date' => Carbon::create(2025, 4, 2, 8, 0, 0), // 2 April 2025, 08:00 AM
                'status' => 'published',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }
    }
}