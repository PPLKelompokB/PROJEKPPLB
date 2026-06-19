<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FullJourneyAdminDocumentationDuskTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * E2E Full Journey: Organizer Register -> Create Event -> Upload Doc -> Admin Verify
     */
    public function test_Full_User_Journey_Admin_Documentation()
    {
        // Setup Admin account in background (karena admin tidak bisa diregister lewat UI)
        $admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin_e2e@test.com',
            'role' => 'admin',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->driver->manage()->deleteAllCookies();

            // 1. REGISTER ORGANIZER
            $browser->visit('/register')
                    ->type('name', 'Budi Organizer')
                    ->type('email', 'budi_org@test.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    // Radio button role
                    ->script("document.querySelector('input[value=\"organizer\"]').checked = true;");
            $browser->press('Register')
                    ->waitForText('Dashboard', 10)
                    ->assertPathIs('/organizer/dashboard');

            // 2. CREATE EVENT
            $browser->visit('/events/create')
                    ->type('title', 'Beach Cleanup Full Journey')
                    ->type('location', 'Pantai Indah Kapuk')
                    ->type('description', 'This is a description for the event.')
                    ->select('duration', '2')
                    ->type('quota', '50')
                    ->type('meeting_point', 'Gerbang Utama')
                    ->type('contact_person', 'Budi')
                    ->type('phone', '08123456789');
            // Set Date ke MASA LALU agar event muncul di halaman dokumentasi, dan Time via JS
            $browser->driver->executeScript(
                "document.querySelector('input[name=date]').value = '2026-01-15';" .
                "document.querySelector('input[name=time]').value = '08:00';"
            );
            // Attach image
            $browser->attach('image', public_path('dummy.png'))
                    ->press('Create Event')
                    ->waitForText('Beach Cleanup Full Journey', 10);

            // 3. UPLOAD DOCUMENTATION
            // Organizer documentation hanya menampilkan event yang sudah berlalu.
            // Kita navigasi langsung ke halaman show event (ID=1).
            $browser->visit('/organizer/documentation')
                    ->waitForText('Beach Cleanup Full Journey', 10)
                    ->clickLink('Manage Documentation')
                    ->waitForText('Upload Documentation', 10)
                    ->attach('file', public_path('dummy.png'))
                    ->type('note', 'Ini laporan dokumentasi E2E')
                    ->press('Upload Documentation')
                    ->waitForText('Documentation uploaded successfully.', 10)
                    // Badge Pending terlihat di halaman (CSS uppercase, DOM text = 'Pending')
                    ->assertSee('Ini laporan dokumentasi E2E');

            // 4. LOGOUT ORGANIZER
            $browser->visit('/organizer/dashboard')
                    ->press('Logout')
                    ->waitForText('Login', 10);

            // 5. LOGIN ADMIN
            $browser->visit('/login')
                    ->type('email', 'admin_e2e@test.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->waitForText('Admin Dashboard', 10);

            // 6. ADMIN APPROVE DOCUMENTATION
            $browser->visit('/admin/documentation')
                    ->waitForText('Beach Cleanup Full Journey', 10)
                    ->clickLink('Review')
                    ->waitForText('Ini laporan dokumentasi E2E', 10)
                    ->click("button[onclick*=\"openModal('approved'\"]") 
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    ->waitForText('Approved', 10)
                    ->assertSee('Approved');
        });
    }
}
