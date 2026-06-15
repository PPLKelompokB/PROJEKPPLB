<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KBP115Test extends DuskTestCase
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function makeVolunteer(): User
    {
        return User::create([
            'name'     => 'Volunteer KBP115 ' . uniqid(),
            'email'    => 'vol' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer',
        ]);
    }

    private function makeOrganizer(): User
    {
        return User::create([
            'name'     => 'Organizer KBP115',
            'email'    => 'org' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);
    }

    private function makeEvent(User $organizer, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'organizer_id'  => $organizer->id,
            'title'         => 'Test Event ' . uniqid(),
            'description'   => 'Deskripsi event untuk testing.',
            'location'      => 'Pantai Kuta, Bali',
            'event_date'    => now()->addDays(10)->format('Y-m-d H:i:s'),
            'duration'      => 3,
            'quota'         => 50,
            'status'        => 'published',
            'meeting_point' => 'Main Gate',
        ], $overrides));
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  REGISTERED EVENT (TC01 – TC04)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP115-TC01: Volunteer melihat daftar event yang telah didaftarkan
     */
    public function test_KBP115_TC01_view_registered_event_list()
    {
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();
        $event     = $this->makeEvent($organizer, ['title' => 'Beach Cleanup TC01']);

        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered',
        ]);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Registered Events')
                    ->assertSee('Beach Cleanup TC01');
        });
    }

    /**
     * KBP115-TC02: Volunteer belum pernah mendaftar event
     */
    public function test_KBP115_TC02_empty_registered_event_list()
    {
        $volunteer = $this->makeVolunteer();

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Belum ada event yang terdaftar.');
        });
    }

    /**
     * KBP115-TC03: Mencari event yang tersedia pada daftar registered event
     */
    public function test_KBP115_TC03_search_registered_event_found()
    {
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();

        $event1 = $this->makeEvent($organizer, ['title' => 'Beach Cleanup Alpha']);
        $event2 = $this->makeEvent($organizer, ['title' => 'River Cleanup Beta']);

        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event1->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event2->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->type('search', 'Beach')
                    ->keys('input[name="search"]', '{enter}')
                    ->waitForText('Beach Cleanup Alpha', 10)
                    ->assertSee('Beach Cleanup Alpha')
                    ->assertDontSee('River Cleanup Beta');
        });
    }

    /**
     * KBP115-TC04: Mencari event yang tidak ada pada daftar registered event
     */
    public function test_KBP115_TC04_search_registered_event_not_found()
    {
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();

        $event = $this->makeEvent($organizer, ['title' => 'Beach Cleanup Exist']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->type('search', 'EventYangTidakAda')
                    ->keys('input[name="search"]', '{enter}')
                    ->waitForText('Tidak ada event yang sesuai dengan pencarian', 10)
                    ->assertSee('Tidak ada event yang sesuai dengan pencarian.');
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  EVENT HISTORY (TC05 – TC08)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP115-TC05: Volunteer melihat daftar riwayat event yang pernah diikuti
     */
    public function test_KBP115_TC05_view_event_history()
    {
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();

        // Event yang sudah selesai (tanggal lampau)
        $event = $this->makeEvent($organizer, [
            'title'      => 'Past Beach Cleanup TC05',
            'event_date' => now()->subDays(5)->format('Y-m-d H:i:s'),
        ]);

        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered',
        ]);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->assertSee('Event History')
                    ->assertSee('Past Beach Cleanup TC05');
        });
    }

    /**
     * KBP115-TC06: Volunteer belum memiliki riwayat event
     */
    public function test_KBP115_TC06_empty_event_history()
    {
        $volunteer = $this->makeVolunteer();

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->assertSee('Belum ada riwayat event');
        });
    }

    /**
     * KBP115-TC07: Mencari event yang tersedia pada riwayat event
     */
    public function test_KBP115_TC07_search_event_history_found()
    {
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();

        $event1 = $this->makeEvent($organizer, [
            'title'      => 'History Beach Alpha',
            'event_date' => now()->subDays(10)->format('Y-m-d H:i:s'),
        ]);
        $event2 = $this->makeEvent($organizer, [
            'title'      => 'History River Beta',
            'event_date' => now()->subDays(8)->format('Y-m-d H:i:s'),
        ]);

        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event1->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event2->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->type('search', 'Beach')
                    ->keys('input[name="search"]', '{enter}')
                    ->waitForText('History Beach Alpha', 10)
                    ->assertSee('History Beach Alpha')
                    ->assertDontSee('History River Beta');
        });
    }

    /**
     * KBP115-TC08: Mencari event yang tidak ada pada riwayat event
     */
    public function test_KBP115_TC08_search_event_history_not_found()
    {
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();

        $event = $this->makeEvent($organizer, [
            'title'      => 'History Exist Event',
            'event_date' => now()->subDays(10)->format('Y-m-d H:i:s'),
        ]);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->type('search', 'EventTidakAda')
                    ->keys('input[name="search"]', '{enter}')
                    ->waitForText('Tidak ada event yang sesuai dengan pencarian', 10)
                    ->assertSee('Tidak ada event yang sesuai dengan pencarian.');
        });
    }
}
