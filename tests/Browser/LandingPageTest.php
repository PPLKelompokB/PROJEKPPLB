<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Event;

class LandingPageTest extends DuskTestCase
{
    // Jika Anda menjalankan test ini di environment khusus testing, Anda dapat meng-uncomment baris di bawah ini:
    // use DatabaseMigrations; 

    /**
     * TC-106-01: (Alur Utama) User membuka halaman utama OceanCare melalui URL yang valid.
     */
    public function test_landing_page_shows_complete_sections()
    {
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
     * TC-106-02: (Alur Utama) User menekan tombol “Join as Volunteer” pada hero section.
     */
    public function test_join_as_volunteer_redirects_to_registration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Join as Volunteer')
                    ->pause(1000)
                    ->assertPathIs('/register');
        });
    }

    /**
     * TC-106-03: (Validasi Data) User membuka section “Featured Upcoming Events”.
     */
    public function test_featured_events_displays_event_cards()
    {
        // Pastikan ada setidaknya satu event untuk ditampilkan
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
     * TC-106-04: (Alur Alternatif) User membuka Landing Page ketika belum terdapat featured event pada database.
     */
    public function test_empty_state_for_featured_events()
    {
        // PERHATIAN: Test ini menghapus data event untuk memicu "empty state".
        // Sangat disarankan untuk menjalankan Dusk pada environment/database khusus testing (seperti .env.dusk.local).
        
        $events = Event::all();
        Event::query()->delete();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Featured Upcoming Events')
                    ->assertSee('No upcoming events right now. Check back soon!');
        });

        // Mengembalikan data events secara sederhana (mungkin tidak sempurna jika ada relasi yang kompleks)
        if ($events->count() > 0) {
            foreach ($events as $event) {
                $eventData = $event->toArray();
                Event::insert($eventData);
            }
        }
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
