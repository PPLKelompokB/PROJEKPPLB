<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewEventParticipantsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * TC-PBI11-01: Organizer (Pemilik Event) melihat daftar peserta event miliknya.
     */
    public function test_organizer_can_view_their_event_participants()
    {
        $organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $event = Event::create(['organizer_id' => $organizer->id, 'title' => 'Event', 'description' => 'Desc', 'event_date' => now()->addDays(5), 'location' => 'Loc', 'duration' => 2, 'quota' => 10, 'status' => 'published', 'image' => '']);
        
        $volunteer = User::create(['name' => 'Vol', 'email' => 'vol'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        EventRegistration::create([
            'user_id' => $volunteer->id,
            'event_id' => $event->id
        ]);

        $this->browse(function (Browser $browser) use ($organizer, $event, $volunteer) {
            $browser->loginAs($organizer)
                    ->visitRoute('events.participants', ['id' => $event->id])
                    ->assertSee($event->title)
                    ->assertSee('Event Participants')
                    ->assertSee($volunteer->name)
                    ->assertSee($volunteer->email);
        });
    }

    /**
     * TC-PBI11-02: Organizer tidak bisa melihat daftar peserta dari event organizer lain.
     */
    public function test_organizer_cannot_view_other_organizer_event_participants()
    {
        $organizer1 = User::create(['name' => 'Org1', 'email' => 'org1'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $organizer2 = User::create(['name' => 'Org2', 'email' => 'org2'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $event = Event::create(['organizer_id' => $organizer1->id, 'title' => 'Event', 'description' => 'Desc', 'event_date' => now()->addDays(5), 'location' => 'Loc', 'duration' => 2, 'quota' => 10, 'status' => 'published', 'image' => '']);

        $this->browse(function (Browser $browser) use ($organizer2, $event) {
            $browser->loginAs($organizer2)
                    ->visit('/events/' . $event->id . '/participants');
                    
            // Memastikan halamannya ter-redirect atau forbidden (403)
            $response = $browser->driver->getPageSource();
            $this->assertTrue(
                str_contains($response, '403') || 
                str_contains($response, 'Forbidden') ||
                !str_contains($browser->driver->getCurrentUrl(), '/participants')
            );
        });
    }

    /**
     * TC-PBI11-03: Guest dialihkan ke halaman login saat mencoba mengakses halaman peserta.
     */
    public function test_guest_is_redirected_to_login()
    {
        $organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $event = Event::create(['organizer_id' => $organizer->id, 'title' => 'Event', 'description' => 'Desc', 'event_date' => now()->addDays(5), 'location' => 'Loc', 'duration' => 2, 'quota' => 10, 'status' => 'published', 'image' => '']);

        $this->browse(function (Browser $browser) use ($event) {
            $browser->logout()
                    ->visit('/events/' . $event->id . '/participants')
                    ->assertRouteIs('login'); // Pastikan diarahkan ke route name 'login'
        });
    }

    /**
     * TC-PBI11-04: Volunteer dan Admin ditolak oleh middleware saat mengakses halaman organizer.
     */
    public function test_volunteer_and_admin_cannot_access_participants_page()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => 'vol'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $admin = User::create(['name' => 'Admin', 'email' => 'admin'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'admin']);
        $organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $event = Event::create(['organizer_id' => $organizer->id, 'title' => 'Event', 'description' => 'Desc', 'event_date' => now()->addDays(5), 'location' => 'Loc', 'duration' => 2, 'quota' => 10, 'status' => 'published', 'image' => '']);

        $this->browse(function (Browser $browser) use ($volunteer, $admin, $event) {
            // Volunteer
            $browser->loginAs($volunteer)
                    ->visit('/events/' . $event->id . '/participants');
            $responseVol = $browser->driver->getPageSource();
            $this->assertTrue(str_contains($responseVol, '403') || str_contains($responseVol, 'Unauthorized') || str_contains($responseVol, 'Forbidden'));
            
            // Admin
            $browser->loginAs($admin)
                    ->visit('/events/' . $event->id . '/participants');
            $responseAdmin = $browser->driver->getPageSource();
            $this->assertTrue(str_contains($responseAdmin, '403') || str_contains($responseAdmin, 'Unauthorized') || str_contains($responseAdmin, 'Forbidden'));
        });
    }

    /**
     * TC-PBI11-05: Organizer melihat state kosong (Empty State) jika tidak ada pendaftar.
     */
    public function test_organizer_views_empty_participants_list()
    {
        $organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $event = Event::create(['organizer_id' => $organizer->id, 'title' => 'Event', 'description' => 'Desc', 'event_date' => now()->addDays(5), 'location' => 'Loc', 'duration' => 2, 'quota' => 10, 'status' => 'published', 'image' => '']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visitRoute('events.participants', ['id' => $event->id])
                    ->assertSee('No participants registered yet.');
        });
    }

    /**
     * TC-PBI11-06: Organizer melihat navigasi pagination ketika pendaftar melebihi limit per halaman.
     */
    public function test_organizer_views_participants_with_pagination()
    {
        $organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $event = Event::create(['organizer_id' => $organizer->id, 'title' => 'Event', 'description' => 'Desc', 'event_date' => now()->addDays(5), 'location' => 'Loc', 'duration' => 2, 'quota' => 10, 'status' => 'published', 'image' => '']);

        // Buat 15 pendaftar untuk mentrigger pagination
        for ($i = 0; $i < 15; $i++) {
            $user = User::create(['name' => 'Vol'.$i, 'email' => 'vol'.$i.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
            EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id
            ]);
        }

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visitRoute('events.participants', ['id' => $event->id])
                    ->assertSee('Showing 1 to 5 of 15 participants'); // Limit pagination = 5
                    
            // Pastikan elemen navigasi page muncul
            $browser->assertPresent('a[href*="page=2"]'); // Navigasi link page 2
        });
    }
}
