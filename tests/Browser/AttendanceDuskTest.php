<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AttendanceDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function makeOrganizer()
    {
        return User::create([
            'name' => 'Org',
            'email' => 'org_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);
    }

    private function makeVolunteer($name = 'Vol')
    {
        return User::create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)) . '_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);
    }

    private function makeEvent($organizer)
    {
        return Event::create([
            'organizer_id' => $organizer->id,
            'title'        => 'Dusk Attendance Event',
            'description'  => 'Test description',
            'location'     => 'Pantai Kuta',
            // Set the event to start exactly now so the buttons show up
            'event_date'   => now()->subMinutes(30)->format('Y-m-d H:i:s'),
            'duration'     => 2,
            'quota'        => 50,
            'status'       => 'published',
        ]);
    }

    public function test_PBI12_TC11_ViewParticipantList()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Budi Santoso');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->loginAs($org)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Event Participants')
                    ->assertSee('Budi Santoso');
        });
    }

    public function test_PBI12_TC01_MarkPresent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->loginAs($org)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Mark Present')
                    ->press('Mark Present')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate')
                    ->assertSee('Present');
        });
    }

    public function test_PBI12_TC02_MarkAbsent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->loginAs($org)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Mark Absent')
                    ->press('Mark Absent')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate')
                    ->assertSee('Absent');
        });
    }

    public function test_PBI12_TC03_UpdatePresentToAbsent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);
        
        // Initial state is present
        Attendance::create([
            'event_id' => $event->id,
            'user_id' => $vol->id,
            'status' => 'present',
            'is_counted' => true
        ]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->loginAs($org)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Present')
                    ->press('Mark Absent') // Because UI now supports changing back
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate')
                    ->assertSee('Absent');
        });
    }

    public function test_PBI12_TC04_UpdateAbsentToPresent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);
        
        // Initial state is absent
        Attendance::create([
            'event_id' => $event->id,
            'user_id' => $vol->id,
            'status' => 'absent',
            'is_counted' => false
        ]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->loginAs($org)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Absent')
                    ->press('Mark Present')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate')
                    ->assertSee('Present');
        });
    }
}
