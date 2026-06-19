<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;

class RegisteredEventTest extends DuskTestCase
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function createVolunteer($suffix = '')
    {
        return User::create([
            'name'     => 'Volunteer ' . $suffix,
            'email'    => 'vol' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer'
        ]);
    }

    private function createEvent($overrides = [])
    {
        $organizer = User::firstOrCreate(['email' => 'org@test.com'], [
            'name'     => 'Org',
            'password' => bcrypt('password'),
            'role'     => 'organizer'
        ]);
        
        $defaults = [
            'organizer_id'  => $organizer->id,
            'title'         => 'Test Event ' . uniqid(),
            'description'   => 'Test Desc',
            'location'      => 'Test Loc',
            'event_date'    => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration'      => 2,
            'quota'         => 50,
            'status'        => 'published',
            'meeting_point' => 'Test Point'
        ];
        
        return Event::create(array_merge($defaults, $overrides));
    }

    // ─── KBP115-TC01: Melihat daftar event yang telah didaftarkan ─────────────

    public function test_KBP115_TC01_view_registered_event_list()
    {
        $volunteer = $this->createVolunteer('TC01');
        $event = $this->createEvent(['title' => 'Beach Cleanup TC01']);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee($event->title);
        });
    }

    // ─── KBP115-TC02: Melihat daftar event saat belum pernah mendaftar ────────

    public function test_KBP115_TC02_empty_registered_events()
    {
        $volunteer = $this->createVolunteer('TC02');

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Belum ada event yang terdaftar');
        });
    }

    // ─── KBP115-TC03: Mencari event yang tersedia ─────────────────────────────

    public function test_KBP115_TC03_search_registered_event_found()
    {
        $volunteer = $this->createVolunteer('TC03');
        $event = $this->createEvent(['title' => 'Tree Planting TC03']);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->type('search', 'Tree Planting')
                    ->keys('input[name="search"]', '{enter}')
                    ->assertSee($event->title);
        });
    }

    // ─── KBP115-TC04: Mencari event yang tidak ada ────────────────────────────

    public function test_KBP115_TC04_search_registered_event_not_found()
    {
        $volunteer = $this->createVolunteer('TC04');
        $event = $this->createEvent(['title' => 'Tree Planting TC04']);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->type('search', 'InvalidEventName')
                    ->keys('input[name="search"]', '{enter}')
                    ->assertSee('Tidak ada event yang sesuai dengan pencarian');
        });
    }

    // ─── KBP115-TC05: Melihat daftar riwayat event yang pernah diikuti ────────

    public function test_KBP115_TC05_view_event_history_list()
    {
        $volunteer = $this->createVolunteer('TC05');
        // Past event
        $event = $this->createEvent([
            'title'      => 'Past River Cleanup TC05',
            'event_date' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s')
        ]);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->assertSee($event->title);
        });
    }

    // ─── KBP115-TC06: Melihat daftar riwayat saat belum memiliki riwayat ──────

    public function test_KBP115_TC06_empty_event_history()
    {
        $volunteer = $this->createVolunteer('TC06');

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->assertSee('Belum ada riwayat event');
        });
    }

    // ─── KBP115-TC07: Mencari event yang tersedia pada riwayat event ──────────

    public function test_KBP115_TC07_search_event_history_found()
    {
        $volunteer = $this->createVolunteer('TC07');
        $event = $this->createEvent([
            'title'      => 'Past Forest Cleanup TC07',
            'event_date' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s')
        ]);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->type('search', 'Forest Cleanup')
                    ->keys('input[name="search"]', '{enter}')
                    ->assertSee($event->title);
        });
    }

    // ─── KBP115-TC08: Mencari event yang tidak ada pada riwayat event ─────────

    public function test_KBP115_TC08_search_event_history_not_found()
    {
        $volunteer = $this->createVolunteer('TC08');
        $event = $this->createEvent([
            'title'      => 'Past Forest Cleanup TC08',
            'event_date' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s')
        ]);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/history')
                    ->type('search', 'InvalidHistoryEvent')
                    ->keys('input[name="search"]', '{enter}')
                    ->assertSee('Tidak ada event yang sesuai dengan pencarian');
        });
    }
}
