<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Models\Point;

class AdminDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    private function makeAdmin(): User
    {
        return User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    private function makeOrganizer(): User
    {
        return User::create([
            'name'     => 'Organizer',
            'email'    => 'org_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);
    }

    private function makeVolunteer(): User
    {
        return User::create([
            'name'     => 'Volunteer',
            'email'    => 'vol_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer',
            'points'   => 0,
        ]);
    }

    private function makeEvent(User $organizer, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'title'        => 'Test Event ' . uniqid(),
            'description'  => 'Test description',
            'location'     => 'Pantai Kuta',
            'event_date'   => now()->subDays(3)->format('Y-m-d H:i:s'),
            'duration'     => 2,
            'quota'        => 50,
            'status'       => 'published',
        ], $overrides));
    }

    private function makeDocumentation(Event $event, string $status = 'pending'): Documentation
    {
        return Documentation::create([
            'event_id'    => $event->id,
            'organizer_id' => $event->organizer_id,
            'file_path'   => 'documentations/test.jpg',
            'status'      => $status,
            'note'        => 'Test documentation',
        ]);
    }

    /**
     * TC-ADM-01: Mengisi kolom pencarian "Search events..."
     */
    public function test_search_events_and_pagination()
    {
        $admin     = $this->makeAdmin();
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Surabaya Clean Up']);
        $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Event Management')
                    ->type('search', $event->title)
                    ->keys('input[name="search"]', '{enter}')
                    ->pause(1000)
                    ->assertSee($event->title)
                    ->assertQueryStringHas('search', $event->title);
        });
    }

    /**
     * TC-ADM-02: Mengklik tombol ikon pensil (Verify/Edit) pada baris event
     */
    public function test_click_pencil_icon_redirects_to_manage_documentation()
    {
        $admin     = $this->makeAdmin();
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer);
        $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->click("a[href='" . route('admin.documentation.show', $event->id) . "']")
                    ->pause(1000)
                    ->assertPathIs('/admin/documentation/' . $event->id)
                    ->assertSee('Event Documentation');
        });
    }

    /**
     * TC-ADM-03: Mengklik tombol "View" pada file dokumentasi
     */
    public function test_click_view_documentation_opens_new_tab()
    {
        $admin     = $this->makeAdmin();
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer);
        $doc       = $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->assertSee('View')
                    ->clickLink('View')
                    ->pause(1000);

            $window = collect($browser->driver->getWindowHandles())->last();
            $browser->driver->switchTo()->window($window);
            $browser->assertUrlIs(asset('storage/' . $doc->file_path));
        });
    }

    /**
     * TC-ADM-04: Mengklik tombol "Approve" pada file dokumentasi
     */
    public function test_approve_single_documentation()
    {
        $admin     = $this->makeAdmin();
        $organizer = $this->makeOrganizer();
        $volunteer = $this->makeVolunteer();
        $event     = $this->makeEvent($organizer, ['duration' => 2]);
        $doc       = $this->makeDocumentation($event, 'pending');

        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered',
        ]);
        Attendance::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'present',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc, $volunteer) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->assertSee('Pending')
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(500)
                    ->assertSee('Are you sure you want to approve this documentation?')
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    ->assertSee('Approved');

            $this->assertTrue(Point::where('event_id', $event->id)->where('user_id', $volunteer->id)->exists());
            $this->assertEquals(20, $volunteer->fresh()->points);

            $browser->visit('/admin/dashboard')
                    ->assertSee('Approved');
        });
    }

    /**
     * TC-ADM-05: Mengklik tombol "Approve All Pending"
     */
    public function test_approve_all_pending_documentations()
    {
        $admin     = $this->makeAdmin();
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer);

        $this->makeDocumentation($event, 'pending');
        $this->makeDocumentation($event, 'pending');
        $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->assertSee('Approve All Pending')
                    ->click("button[onclick=\"openModal('approved')\"]")
                    ->pause(500)
                    ->assertSee('Are you sure you want to approve these documentations?')
                    ->click('#confirmModalBtn')
                    ->pause(3000)
                    ->assertSee('Approved');
        });
    }
}
