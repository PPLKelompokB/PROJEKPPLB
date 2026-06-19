<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisteredEventTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function createEvent($overrides = []) {
        $organizer = User::firstOrCreate(['email' => 'org@test.com'], [
            'name' => 'Org', 'password' => bcrypt('password'), 'role' => 'organizer'
        ]);
        
        $defaults = [
            'organizer_id' => $organizer->id,
            'title' => 'Test Event',
            'description' => 'Test Desc',
            'location' => 'Test Loc',
            'event_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 50,
            'status' => 'published',
            'meeting_point' => 'Test Point'
        ];
        
        return Event::create(array_merge($defaults, $overrides));
    }

    /**
     * PBI09-TC01-ViewRegisteredEventList
     */
    public function test_volunteer_can_view_registered_event_list()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['title' => 'Beach Cleanup 1']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Registered Events')
                    ->assertSee($event->title)
                    ->assertSee($event->location);
        });
    }

    /**
     * PBI09-TC02-ViewRegisteredEventDetail
     */
    public function test_volunteer_can_view_registered_event_detail()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['title' => 'Detail Test', 'quota' => 50]);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee($event->location)
                    ->assertSee($event->description)
                    ->assertSee('50 Volunteers'); // Quota
        });
    }

    /**
     * PBI09-TC03-SearchRegisteredEvent
     */
    public function test_search_registered_event()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event1 = $this->createEvent(['title' => 'Beach Cleanup A']);
        $event2 = $this->createEvent(['title' => 'River Cleanup B']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event1->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event2->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->type('search', 'Beach')
                    ->keys('input[name="search"]', '{enter}')
                    ->assertSee('Beach Cleanup A')
                    ->assertDontSee('River Cleanup B');
        });
    }

    /**
     * PBI09-TC04-EmptyRegisteredEventList
     */
    public function test_empty_registered_event_list()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Belum ada event yang terdaftar.');
        });
    }

    /**
     * PBI09-TC05-UnauthenticatedAccess
     */
    public function test_unauthenticated_access_redirects_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/volunteer/registered-events')
                    ->assertPathIs('/login');
        });
    }

    /**
     * PBI09-TC06-NonVolunteerAccess
     */
    public function test_non_volunteer_access_gets_403()
    {
        $admin = User::create(['name' => 'Admin', 'email' => uniqid().'@a.com', 'password' => bcrypt('password'), 'role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('403'); // Laravel's default 403 page
        });
    }

    /**
     * PBI09-TC07-InvalidEventId
     */
    public function test_invalid_event_id_returns_404()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events/99999')
                    ->assertSee('404')
                    ->assertSee('Not Found'); // Default Laravel 404 text
        });
    }

    /**
     * PBI09-TC08-PaginationRegisteredEvent
     */
    public function test_pagination_registered_event()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        
        // Create 15 events
        for ($i = 0; $i < 15; $i++) {
            $event = $this->createEvent(['title' => "Event $i"]);
            EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);
        }

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertPresent('nav[role="navigation"]') // Tailwind pagination nav
                    ->script("document.querySelector('a[href*=\"page=2\"]').click()"); // Click via JS to avoid visibility issues
            
            // Wait for load
            $browser->pause(1000)
                    ->assertPathIs('/volunteer/registered-events')
                    ->assertQueryStringHas('page', '2');
        });
    }

    /**
     * PBI09-TC09-SearchNoResult
     */
    public function test_search_no_result()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['title' => 'Beach Cleanup']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->type('search', 'Nonexistent')
                    ->keys('input[name="search"]', '{enter}')
                    ->assertSee('Tidak ada event yang sesuai dengan filter yang dipilih.');
        });
    }

    /**
     * PBI09-TC10-ViewUpcomingRegisteredEvent
     */
    public function test_view_upcoming_registered_event_sorting()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $eventFar = $this->createEvent(['title' => 'Far Event', 'event_date' => now()->addDays(10)->format('Y-m-d H:i:s')]);
        $eventClose = $this->createEvent(['title' => 'Close Event', 'event_date' => now()->addDays(2)->format('Y-m-d H:i:s')]);
        
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventFar->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventClose->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events');
            
            $text = $browser->text('.grid'); // The grid container
            $this->assertTrue(strpos($text, 'Close Event') < strpos($text, 'Far Event'));
        });
    }

    /**
     * PBI09-TC11-ViewPastRegisteredEvent
     */
    public function test_view_past_registered_event()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['title' => 'Past Event', 'event_date' => now()->subDays(5)->format('Y-m-d H:i:s')]);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Past Event')
                    ->assertSee('Completed'); // Because event is in the past
        });
    }

    /**
     * PBI09-TC12-EventDeletedByOrganizer
     */
    public function test_deleted_event_is_not_accessible()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['title' => 'To Be Deleted']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);
        
        $event->delete();

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertDontSee('To Be Deleted'); 
            
            $browser->visit('/volunteer/registered-events/' . $event->id)
                    ->assertSee('404');
        });
    }

    /**
     * PBI09-TC13-ViewRegistrationStatus
     */
    public function test_view_registration_status()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['event_date' => now()->addDays(5)->format('Y-m-d H:i:s')]);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Upcoming');
        });
    }

    /**
     * PBI09-TC14-LoadRegisteredEventList
     */
    public function test_load_registered_event_list_without_error()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        for ($i = 0; $i < 20; $i++) {
            $event = $this->createEvent(['title' => "E $i"]);
            EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);
        }

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Registered Events');
        });
    }

    /**
     * PBI09-TC15-RefreshRegisteredEventList
     */
    public function test_refresh_registered_event_list()
    {
        $volunteer = User::create(['name' => 'Vol', 'email' => uniqid().'@v.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $event = $this->createEvent(['title' => 'Refresh Test']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/volunteer/registered-events')
                    ->assertSee('Refresh Test')
                    ->refresh()
                    ->assertSee('Refresh Test');
        });
    }
}
