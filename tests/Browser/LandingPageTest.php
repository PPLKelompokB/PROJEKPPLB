<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Event;
use App\Models\User;

class LandingPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * TC-106-01: (Alur Utama) User membuka halaman utama OceanCare melalui URL yang valid.
     */
    public function test_landing_page_shows_complete_sections()
    {
        // Create a published event so page renders fully
        $organizer = User::create([
            'name'     => 'Organizer Landing',
            'email'    => 'org_landing_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);
        Event::create([
            'organizer_id' => $organizer->id,
            'title'        => 'Landing Test Event',
            'description'  => 'Test event for landing page',
            'location'     => 'Pantai Kuta',
            'event_date'   => now()->addDays(10)->format('Y-m-d H:i:s'),
            'duration'     => 2,
            'quota'        => 50,
            'status'       => 'published',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('OceanCare') // Navbar text / Logo
                    ->assertSee('Protect Our Ocean') // Hero section
                    ->assertSee('Total Volunteers') // Statistics section
                    ->assertSee('Featured Upcoming Events') // Featured events section
                    ->assertSee('Our Environmental Mission'); // Mission section
        });
    }

    /**
     * TC-106-02: (Alur Utama) User menekan tombol "Join as Volunteer" pada hero section.
     */
    public function test_join_as_volunteer_redirects_to_registration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->pause(500)
                    ->click('a[href="' . route('register') . '"]')
                    ->pause(1000)
                    ->assertPathIs('/register');
        });
    }

    /**
     * TC-106-03: (Validasi Data) User membuka section "Featured Upcoming Events".
     */
    public function test_featured_events_displays_event_cards()
    {
        $organizer = User::create([
            'name'     => 'Organizer Featured',
            'email'    => 'org_feat_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'title'        => 'Dusk Test Beach Cleanup',
            'description'  => 'Event for featured section test',
            'location'     => 'Pantai Marina',
            'event_date'   => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration'     => 3,
            'quota'        => 30,
            'status'       => 'published',
        ]);

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
     * TC-106-04: (Alur Alternatif) User membuka Landing Page ketika belum terdapat featured event pada database.
     */
    public function test_empty_state_for_featured_events()
    {
        // No events created, so page should show empty state
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Featured Upcoming Events')
                    ->assertSee('No upcoming events right now. Check back soon!');
        });
    }

    /**
     * TC-106-05: (Error Handling) User mengakses URL Landing Page dengan rute yang salah/tidak valid.
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
