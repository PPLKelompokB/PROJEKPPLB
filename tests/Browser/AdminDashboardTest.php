<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Event;
use App\Models\User;

class AdminDashboardTest extends DuskTestCase
{
    // Jika menjalankan test ini di environment khusus testing, Anda dapat meng-uncomment baris di bawah ini:
    // use DatabaseMigrations;

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
     * TC-AD-01: Admin Dashboard - Tampilan Utama
     */
    public function test_admin_dashboard_shows_main_components()
    {
        $admin = $this->getAdmin();
        
        if (Event::count() == 0) {
            Event::factory()->create();
        }

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Total Users')
                    ->assertSee('Total Events')
                    ->assertSee('Total Finished Events')
                    ->assertSee('Event Management');
        });
    }

    /**
     * TC-AD-02: Admin Dashboard - Fitur Pencarian (Valid)
     */
    public function test_search_valid_keyword()
    {
        $admin = $this->getAdmin();
        $event = Event::first();
        
        if (!$event) {
            $event = Event::factory()->create(['title' => 'Surabaya Clean Up']);
        }

        $this->browse(function (Browser $browser) use ($admin, $event) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->type('search', substr($event->title, 0, 5))
                    ->keys('input[name="search"]', '{enter}')
                    ->pause(1000)
                    ->assertSee($event->title)
                    ->assertQueryStringHas('search', substr($event->title, 0, 5));
        });
    }

    /**
     * TC-AD-03: Admin Dashboard - Fitur Pencarian (Tidak Valid)
     */
    public function test_search_invalid_keyword()
    {
        $admin = $this->getAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->type('search', 'xyz123invalid')
                    ->keys('input[name="search"]', '{enter}')
                    ->pause(1000)
                    ->assertQueryStringHas('search', 'xyz123invalid')
                    ->assertDontSee('Exception');
        });
    }

    /**
     * TC-AD-04: Admin Dashboard - Paginasi & Pencarian
     */
    public function test_pagination_keeps_search_query()
    {
        $admin = $this->getAdmin();
        
        $keyword = 'TestEvent';
        $count = Event::where('title', 'like', "%$keyword%")->count();
        if ($count < 11) {
            Event::factory()->count(11 - $count)->create(['title' => "$keyword " . time()]);
        }

        $this->browse(function (Browser $browser) use ($admin, $keyword) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->type('search', $keyword)
                    ->keys('input[name="search"]', '{enter}')
                    ->pause(1000)
                    ->assertSee($keyword);
            
            $nextLink = $browser->element('a[rel="next"]');
            if ($nextLink) {
                $browser->click('a[rel="next"]')
                        ->pause(1000)
                        ->assertQueryStringHas('search', $keyword)
                        ->assertQueryStringHas('page', '2');
            }
        });
    }

    /**
     * TC-AD-05: Admin Dashboard - Aksi View
     */
    public function test_action_view_redirects_to_event_detail()
    {
        $admin = $this->getAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard');

            $href = $browser->attribute('a[title="View Event"]', 'href');
            $browser->click('a[title="View Event"]')
                    ->pause(1000)
                    ->assertPathIs(parse_url($href, PHP_URL_PATH));
        });
    }

    /**
     * TC-AD-06: Admin Dashboard - Aksi Verify
     */
    public function test_action_verify_redirects_to_manage_documentation()
    {
        $admin = $this->getAdmin();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard');

            $href = $browser->attribute('a[title="Verify Event"]', 'href');
            $browser->click('a[title="Verify Event"]')
                    ->pause(1000)
                    ->assertPathIs(parse_url($href, PHP_URL_PATH));
        });
    }

    /**
     * TC-AD-07: Admin Dashboard - Akses Keamanan
     */
    public function test_unauthorized_access_redirects()
    {
        $volunteer = User::where('role', 'volunteer')->first();
        if (!$volunteer) {
            $volunteer = User::factory()->create(['role' => 'volunteer']);
        }

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->loginAs($volunteer)
                    ->visit('/admin/dashboard')
                    ->pause(1000)
                    ->assertSee('403'); 
        });
    }

    /**
     * TC-AD-08: Admin Dashboard - Empty State
     */
    public function test_empty_state()
    {
        $admin = $this->getAdmin();
        
        $events = Event::all();
        Event::query()->delete();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/admin/dashboard')
                    ->assertSee('Total Events')
                    ->assertDontSee('Exception');
        });

        if ($events->count() > 0) {
            foreach ($events as $event) {
                Event::insert($event->getAttributes());
            }
        }
    }
}
