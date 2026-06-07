<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use App\Models\EventRegistration;
use App\Models\Point;

class AdminDashboardTest extends DuskTestCase
{
    /**
     * Setup a basic admin user for testing.
     */
    private function getAdmin()
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::factory()->create([
                'name' => 'Admin Test',
                'email' => 'admin_test_' . time() . '@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }
        return $admin;
    }

    /**
     * TC-ADM-01: Mengisi kolom pencarian "Search events..."
     */
    public function test_search_events_and_pagination()
    {
        $admin = $this->getAdmin();
        $event = Event::first();
        
        if (!$event) {
            $event = Event::factory()->create(['title' => 'Surabaya Clean Up']);
        }

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
        $admin = $this->getAdmin();
        $event = Event::has('documentations')->first();
        
        if (!$event) {
            $event = Event::first() ?? Event::factory()->create();
            Documentation::factory()->create(['event_id' => $event->id]);
        }

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    // The pencil icon is an anchor with route admin.documentation.show
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
        $admin = $this->getAdmin();
        $event = Event::has('documentations')->first();
        
        if (!$event) {
            $event = Event::factory()->create();
            Documentation::factory()->create(['event_id' => $event->id, 'file_path' => 'test.jpg']);
        }
        $doc = $event->documentations->first();

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->assertSee('View')
                    ->clickLink('View') // Opens in a new tab due to target="_blank"
                    ->pause(1000);
            
            // Switch to the newly opened tab
            $window = collect($browser->driver->getWindowHandles())->last();
            $browser->driver->switchTo()->window($window);
            
            // Assert that the URL matches the asset path of the image
            $browser->assertUrlIs(asset('storage/' . $doc->file_path));
        });
    }

    /**
     * TC-ADM-04: Mengklik tombol "Approve" pada file dokumentasi
     */
    public function test_approve_single_documentation()
    {
        $admin = $this->getAdmin();
        
        // Setup data for point distribution
        $event = Event::factory()->create(['duration' => 2]);
        $volunteer = User::factory()->create(['role' => 'volunteer', 'points' => 0]);
        EventRegistration::factory()->create(['event_id' => $event->id, 'user_id' => $volunteer->id]);
        $doc = Documentation::factory()->create(['event_id' => $event->id, 'status' => 'pending']);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc, $volunteer) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->assertSee('Pending')
                    ->click("button[onclick*='openModal(\"approved\", {$doc->id})']")
                    ->pause(500)
                    ->assertSee('Are you sure you want to approve this documentation?')
                    ->click('#confirmModalBtn')
                    ->pause(2000) // wait for fetch response and reload
                    ->assertSee('Approved')
                    ->assertDontSee('Pending');

            // Assert points were successfully distributed
            $this->assertTrue(Point::where('event_id', $event->id)->where('user_id', $volunteer->id)->exists());
            $this->assertTrue($volunteer->fresh()->points == 20); // 2 hours * 10 points

            // Verify status on dashboard
            $browser->visit('/admin/dashboard')
                    ->assertSee('Approved');
        });
    }

    /**
     * TC-ADM-05: Mengklik tombol "Approve All Pending"
     */
    public function test_approve_all_pending_documentations()
    {
        $admin = $this->getAdmin();
        $event = Event::factory()->create();
        
        // Create multiple pending documentations
        Documentation::factory()->count(3)->create([
            'event_id' => $event->id, 
            'status' => 'pending'
        ]);

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/documentation/' . $event->id)
                    ->assertSee('Approve All Pending')
                    ->click("button[onclick=\"openModal('approved')\"]")
                    ->pause(500)
                    ->assertSee('Are you sure you want to approve these documentations?')
                    ->click('#confirmModalBtn')
                    ->pause(3000) // Wait for all sequential fetch requests to complete
                    ->assertDontSee('Pending')
                    ->assertSee('Approved');
        });
    }
}
