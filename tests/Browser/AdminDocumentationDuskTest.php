<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminDocumentationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function makeAdmin()
    {
        return User::create([
            'name' => 'Admin',
            'email' => 'admin_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);
    }

    private function makeOrganizer()
    {
        return User::create([
            'name' => 'Org',
            'email' => 'org_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);
    }

    private function makeVolunteer()
    {
        return User::create([
            'name' => 'Vol',
            'email' => 'vol_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer',
            'points' => 0
        ]);
    }

    private function makeEvent($organizer, array $overrides = [])
    {
        return Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'title'        => 'Dusk Test Event',
            'description'  => 'Test description',
            'location'     => 'Pantai Kuta',
            'event_date'   => now()->subDays(3)->format('Y-m-d H:i:s'),
            'duration'     => 2,
            'quota'        => 50,
            'status'       => 'published',
        ], $overrides));
    }

    private function makeDocumentation($event, $status = 'pending')
    {
        return Documentation::create([
            'event_id'     => $event->id,
            'organizer_id' => $event->organizer_id,
            'file_path'    => 'doc.jpg',
            'status'       => $status,
            'note'         => 'Test documentation visual'
        ]);
    }

    public function test_PBI14_TC01_ViewDocumentationList()
    {
        $admin = $this->makeAdmin();
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['title' => 'List Event']);
        $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation')
                    ->pause(1000)
                    ->assertSee('Event Documentation')
                    ->assertSee('List Event');
        });
    }

    public function test_PBI14_TC02_ViewDocumentationDetail()
    {
        $admin = $this->makeAdmin();
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['title' => 'Detail Event']);
        $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1000)
                    ->assertSee('Event Documentation');
        });
    }

    public function test_PBI14_TC03_ApproveDocumentation()
    {
        $admin = $this->makeAdmin();
        $org = $this->makeOrganizer();
        $vol = $this->makeVolunteer();
        $event = $this->makeEvent($org, ['duration' => 2]);
        $doc = $this->makeDocumentation($event, 'pending');

        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'registered']);
        Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1000)
                    ->assertSee('Pending')
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    ->assertSee('Are you sure you want to approve this documentation?')
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    ->assertSee('Approved');
        });
    }

    public function test_PBI14_TC04_RejectDocumentation()
    {
        $admin = $this->makeAdmin();
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1000)
                    ->assertSee('Pending')
                    ->click("button[onclick*=\"openModal('rejected', {$doc->id})\"]")
                    ->pause(1000)
                    ->assertSee('Are you sure you want to reject this documentation?')
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    ->assertSee('Rejected');
        });
    }
}
