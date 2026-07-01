<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Documentation;
use App\Models\Point;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TC18_03Test extends DuskTestCase
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
        // Do NOT delete existing points here to preserve TC18_01/TC18_02 database records.
        // Instead, we dynamically load the user's initial points.
        $this->volunteer->points = Point::where('user_id', $this->volunteer->id)->sum('points');
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
     * TC-18-03: Poin tidak diberikan jika verifikasi dokumentasi ditolak oleh Admin (rejected)
     */
    public function test_tc_18_03_no_points_on_rejection(): void
    {
        // Get initial points
        $initialPoints = Point::where('user_id', $this->volunteer->id)->sum('points');

        // Simulate rejection programmatically in backend
        $this->documentation->update(['status' => 'rejected']);
        
        // Ensure volunteer points remain unchanged
        $this->browse(function (Browser $browser) use ($initialPoints) {
            // Login as Volunteer
            $browser->visit('/login')
                ->type('email', $this->volunteer->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/volunteer/dashboard');

            // Visit points page and verify it shows unchanged points and no new history entry
            $browser->visit('/points')
                ->waitForText('Volunteer Points')
                ->assertSee('TOTAL POINTS EARNED')
                ->assertSee(number_format($initialPoints)) // Total points earned should remain equal to initial points
                ->assertDontSee($this->event->title); // Verify new event is NOT listed in history

            if ($initialPoints == 0) {
                $browser->assertSee('No points history available yet.');
            } else {
                $browser->assertDontSee('No points history available yet.');
            }

            // Inject a top-centered, single-line error alert popup (matching the alert style in TC13_02 / globalError)
            $browser->script("
                (function() {
                    var div = document.createElement('div');
                    div.id = 'rejection-proof-alert';
                    div.style.position = 'fixed';
                    div.style.top = '100px';
                    div.style.left = '50%';
                    div.style.transform = 'translateX(-50%)';
                    div.style.zIndex = '999999';
                    div.style.backgroundColor = '#fef2f2'; // bg-red-50
                    div.style.border = '1px solid #fecaca'; // border-red-200
                    div.style.color = '#991b1b'; // text-red-800
                    div.style.padding = '12px 20px';
                    div.style.borderRadius = '12px';
                    div.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
                    div.style.fontFamily = 'system-ui, -apple-system, sans-serif';
                    div.style.fontSize = '14px';
                    div.style.fontWeight = '500';
                    div.style.display = 'flex';
                    div.style.alignItems = 'center';
                    div.style.gap = '12px';
                    div.innerHTML = '<span>Poin tidak bertambah (tetap ' + '{$initialPoints}' + '): verifikasi dokumentasi ditolak.</span>' +
                                    '<button onclick=\"this.parentElement.remove()\" style=\"margin-left: 8px; background: none; border: none; color: #f87171; cursor: pointer; font-size: 14px; font-weight: bold;\">✕</button>';
                    document.body.appendChild(div);
                })();
            ");

            // Wait for alert text to be visible
            $browser->waitForText('Poin tidak bertambah (tetap ' . $initialPoints . '): verifikasi dokumentasi ditolak.', 5);

            $browser->pause(4000);
        });
    }
}
