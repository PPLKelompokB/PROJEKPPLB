<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventDetailTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * TC-PBI6-01: Pengguna mengklik salah satu event dari daftar event (ID Event valid).
     */
    public function test_user_can_view_event_details()
    {
        $organizer = User::create([
            'name' => 'Test Organizer',
            'email' => 'organizer' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Sample Clean Up Event',
            'description' => 'Test event description for cleaning up the beach.',
            'location' => 'Bali Beach',
            'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 50,
            'status' => 'published',
            'meeting_point' => 'Main Gate'
        ]);

        $this->browse(function (Browser $browser) use ($event) {
            $browser->visit('/events')
                    ->clickLink('View Details')
                    ->assertPathIs('/events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee($event->description)
                    ->assertSee($event->location)
                    ->assertSee($event->organizer->name);
        });
    }

    /**
     * TC-PBI6-02: Pengguna mencoba mengakses URL halaman detail event dengan ID yang tidak ada di database (Invalid ID).
     */
    public function test_user_sees_not_found_message_for_invalid_event()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/events/999999')
                    ->assertPathIs('/')
                    ->assertSee('Event tidak ditemukan');
        });
    }

    /**
     * TC-PBI6-03: Pengguna dengan role Organizer mengakses halaman detail event yang ia buat sendiri.
     */
    public function test_organizer_can_see_edit_and_delete_buttons_on_own_event()
    {
        $organizer = User::create([
            'name' => 'Organizer Own',
            'email' => 'org_own' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'My Awesome Event',
            'description' => 'Organizer viewing own event',
            'location' => 'Jakarta',
            'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 50,
            'status' => 'published',
            'meeting_point' => 'Lobby'
        ]);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee('Edit Event')
                    ->assertSee('Hapus Event');
        });
    }

    /**
     * TC-PBI6-04: Pengguna dengan role Volunteer mengakses halaman detail event yang belum ia ikuti.
     */
    public function test_volunteer_can_see_registration_button()
    {
        $organizer = User::create([
            'name' => 'Organizer',
            'email' => 'org' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $volunteer = User::create([
            'name' => 'Volunteer',
            'email' => 'vol' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Community Clean Up',
            'description' => 'Volunteer registration test',
            'location' => 'Surabaya',
            'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration' => 3,
            'quota' => 50,
            'status' => 'published',
            'meeting_point' => 'Park'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee('Register Now'); // or Daftar Event according to UI
        });
    }

    /**
     * TC-PBI6-05: Pengguna mengakses halaman detail event yang kuota pesertanya sudah penuh.
     */
    public function test_user_sees_event_full_when_quota_is_reached()
    {
        $organizer = User::create([
            'name' => 'Organizer Full',
            'email' => 'org_full' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Limited Clean Up',
            'description' => 'Quota test',
            'location' => 'Bandung',
            'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 1,
            'status' => 'published',
            'meeting_point' => 'Station'
        ]);

        $volunteer1 = User::create([
            'name' => 'Volunteer 1',
            'email' => 'vol1' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);

        EventRegistration::create([
            'user_id' => $volunteer1->id,
            'event_id' => $event->id,
            'status' => 'registered'
        ]);

        $volunteer2 = User::create([
            'name' => 'Volunteer 2',
            'email' => 'vol2' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);

        $this->browse(function (Browser $browser) use ($volunteer2, $event) {
            $browser->loginAs($volunteer2)
                    ->visit('/events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee('Event Full'); // or Kuota Penuh
        });
    }

    /**
     * TC-PBI6-06: Pengguna yang belum login (Guest) mengakses halaman detail event.
     */
    public function test_guest_is_redirected_to_login_when_registering()
    {
        $organizer = User::create([
            'name' => 'Organizer Guest View',
            'email' => 'org_guest' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Guest View Event',
            'description' => 'Guest view test',
            'location' => 'Yogyakarta',
            'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 50,
            'status' => 'published',
            'meeting_point' => 'Monument'
        ]);

        $this->browse(function (Browser $browser) use ($event) {
            $browser->logout()
                    ->visit('/events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee('Login to Register')
                    ->clickLink('Login to Register')
                    ->assertPathIs('/login');
        });
    }
}
