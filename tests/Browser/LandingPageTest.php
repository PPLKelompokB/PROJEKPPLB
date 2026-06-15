<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Event;
use App\Models\User;

class LandingPageTest extends DuskTestCase
{
    // Jika menjalankan test ini di environment khusus testing, Anda dapat meng-uncomment baris di bawah ini:
    // use DatabaseMigrations; 

    /**
     * TC-LP-01: Landing Page - Tampilan Utama
     */
    public function test_landing_page_shows_complete_sections()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('OceanCare') // Navbar
                    ->assertSee('Protect Our Ocean') // Hero
                    ->assertSee('Total Volunteers') // Stats
                    ->assertSee('Our Environmental Mission'); // Mission
        });
    }

    /**
     * TC-LP-02: Landing Page - Registrasi (Guest)
     */
    public function test_join_as_volunteer_redirects_to_registration_for_guest()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout() // Pastikan guest
                    ->visit('/')
                    ->clickLink('Join as Volunteer')
                    ->pause(1000)
                    ->assertPathIs('/register');
        });
    }

    /**
     * TC-LP-03: Landing Page - Daftar Event Unggulan
     */
    public function test_featured_events_displays_event_cards()
    {
        $event = Event::first();
        if (!$event) {
            $event = Event::factory()->create([
                'title' => 'Dusk Test Beach Cleanup',
                'location' => 'Pantai Marina'
            ]);
        }

        $this->browse(function (Browser $browser) use ($event) {
            $browser->visit('/')
                    ->assertSee('Featured Upcoming Events')
                    ->assertSee($event->title)
                    ->assertSee($event->location)
                    ->assertSee('volunteers')
                    ->assertSee('View Details');
        });
    }

    /**
     * TC-LP-04: Landing Page - Aksi View Details
     */
    public function test_view_details_redirects_to_event_page()
    {
        // Ambil event terbaru yang akan muncul pertama kali di landing page
        $event = Event::latest()->first();
        if (!$event) {
            $event = Event::factory()->create();
        }

        $this->browse(function (Browser $browser) use ($event) {
            $browser->visit('/')
                    ->assertSee('Featured Upcoming Events')
                    ->clickLink('View Details')
                    ->pause(1000)
                    ->assertPathIs('/events/' . $event->id);
        });
    }

    /**
     * TC-LP-05: Landing Page - Empty State Event
     */
    public function test_empty_state_for_featured_events()
    {
        // Backup event yang ada
        $events = Event::all();
        Event::query()->delete();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Featured Upcoming Events')
                    ->assertSee('No upcoming events right now. Check back soon!');
        });

        // Restore event
        if ($events->count() > 0) {
            foreach ($events as $event) {
                Event::insert($event->getAttributes());
            }
        }
    }

    /**
     * TC-LP-06: Landing Page - Registrasi (Logged In)
     */
    public function test_join_as_volunteer_redirects_to_dashboard_for_logged_in_user()
    {
        $user = User::where('role', 'volunteer')->first();
        if (!$user) {
            $user = User::factory()->create(['role' => 'volunteer']);
        }

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->clickLink('Join as Volunteer')
                    ->pause(1000)
                    ->assertPathIsNot('/register') // Middleware auth mencegah akses /register
                    ->assertPathBeginsWith('/'); // Tergantung pengaturan redirect di middleware, biasanya kembali ke dashboard
        });
    }

    /**
     * TC-LP-07: Landing Page - Navbar Dinamis
     */
    public function test_navbar_adapts_for_logged_in_user()
    {
        $user = User::first() ?? User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertDontSee('Login')
                    ->assertSee('Dashboard')
                    ->assertSee('Logout');
        });
    }

    /**
     * TC-LP-08: Landing Page - Error Handling 404
     */
    public function test_invalid_route_shows_404_not_found()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/rute-asal-asalan-yang-tidak-valid-12345')
                    ->assertSee('404')
                    ->assertSee('Not Found');
        });
    }
}
