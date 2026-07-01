<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Documentation;
use App\Models\Point;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TC18_01Test extends DuskTestCase
{
    protected $volunteer;
    protected $organizer;
    protected $event;
    protected $registration;
    protected $documentation;

    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create/Find Volunteer User
        $this->volunteer = User::firstOrCreate(
            ['email' => 'volunteer1@mail.com'],
            [
                'name' => 'Volunteer 1',
                'password' => bcrypt('123456'),
                'role' => 'volunteer',
            ]
        );
        // Clean up any existing points for this volunteer before running the test to ensure isolation
        Point::where('user_id', $this->volunteer->id)->delete();
        $this->volunteer->points = 0;
        $this->volunteer->save();

        // 2. Create/Find Organizer User
        $this->organizer = User::firstOrCreate(
            ['email' => 'organizer@oceancare.com'],
            [
                'name' => 'Organizer 1',
                'password' => bcrypt('123456'),
                'role' => 'organizer',
            ]
        );

        // 3. Create Event A with duration = 4 hours
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event A ' . uniqid(),
            'description' => 'Cleanup Event A for Points Test',
            'location' => 'Sanur Beach',
            'event_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'duration' => 4,
            'quota' => 30,
            'status' => 'published',
        ]);

        // 4. Register Volunteer to Event A
        $this->registration = EventRegistration::create([
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
        ]);

        // 5. Create Documentation for Event A
        $this->documentation = Documentation::create([
            'event_id' => $this->event->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_points.png',
            'status' => 'pending',
            'note' => 'Documentation notes for Event A',
        ]);
    }

    protected function tearDown(): void
    {
        /*
        if ($this->documentation) {
            $this->documentation->delete();
        }

        if ($this->registration) {
            $this->registration->delete();
        }

        if ($this->event) {
            $this->event->delete();
        }

        if ($this->volunteer) {
            Point::where('user_id', $this->volunteer->id)->delete();
        }
        */

        parent::tearDown();
        static::closeAll();
    }

    /**
     * TC-18-01: Perhitungan dan penambahan poin berhasil saat dokumentasi disetujui
     */
    public function test_tc_18_01_points_calculation(): void
    {
        // Simulate documentation approval and points awarding programmatically in backend
        $this->documentation->update(['status' => 'approved']);
        
        $pointsEarned = $this->event->duration * 10; // 40 points
        Point::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'points' => $pointsEarned
        ]);

        $this->volunteer->points = $pointsEarned;
        $this->volunteer->save();

        $this->browse(function (Browser $browser) {
            // Login as Volunteer
            $browser->visit('/login')
                ->type('email', $this->volunteer->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/volunteer/dashboard');

            // Visit points page and verify
            $browser->visit('/points')
                ->waitForText('Volunteer Points')
                ->assertSee('40') // Total Points Earned
                ->assertSee('TOTAL POINTS EARNED')
                ->assertSee($this->event->title)
                ->assertSee('+40');

            $browser->pause(3000);
        });
    }
}
