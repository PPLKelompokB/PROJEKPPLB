<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FullJourneyAttendanceDuskTest extends DuskTestCase
{

    /**
     * E2E Full Journey: Organizer Register -> Create Event -> Volunteer Register -> Join Event -> Organizer Mark Present
     */
    public function test_Full_User_Journey_Attendance()
    {
        $eventId = null;

        $this->browse(function (Browser $browser) use (&$eventId) {
            $browser->driver->manage()->deleteAllCookies();

            // ==========================================
            // 1. REGISTER ORGANIZER
            // ==========================================
            $browser->visit('/register')
                    ->type('name', 'Andi Organizer')
                    ->type('email', 'andi_org@test.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123');
            $browser->driver->executeScript(
                "document.querySelector('input[value=\"organizer\"]').checked = true;"
            );
            $browser->press('Register')
                    ->waitForText('Dashboard', 10)
                    ->assertPathIs('/organizer/dashboard');

            // ==========================================
            // 2. CREATE EVENT (hari ini = ongoing)
            // ==========================================
            $browser->visit('/events/create')
                    ->type('title', 'Beach Cleanup Attendance E2E')
                    ->type('location', 'Pantai Sanur')
                    ->type('description', 'This is a description for the event.')
                    ->select('duration', '2')
                    ->type('quota', '50')
                    ->type('meeting_point', 'Gerbang Utama Sanur')
                    ->type('contact_person', 'Andi')
                    ->type('phone', '08123456789');

            // Set Date (today) & Time via JS (1 jam lalu agar event ONGOING)
            $time = date('H:i', strtotime('-1 hour'));
            $date = date('Y-m-d');
            $browser->driver->executeScript(
                "document.querySelector('input[name=date]').value = '{$date}';" .
                "document.querySelector('input[name=time]').value = '{$time}';"
            );

            // Attach image dan submit
            $browser->attach('image', public_path('dummy.png'))
                    ->press('Create Event')
                    ->waitForText('Beach Cleanup Attendance E2E', 10);

            // Tangkap Event ID dari URL saat ini (misal: /events/3)
            $currentUrl = $browser->driver->getCurrentURL();
            preg_match('/\/events\/(\d+)/', $currentUrl, $matches);
            $eventId = $matches[1] ?? 1;

            // ==========================================
            // 3. LOGOUT ORGANIZER
            // ==========================================
            $browser->press('Logout')
                    ->waitForText('Login', 10);

            // ==========================================
            // 4. REGISTER VOLUNTEER
            // ==========================================
            $browser->visit('/register')
                    ->type('name', 'Budi Volunteer')
                    ->type('email', 'budi_vol@test.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123');
            // Volunteer role (default, tapi eksplisit)
            $browser->driver->executeScript(
                "document.querySelector('input[value=\"volunteer\"]').checked = true;"
            );
            $browser->press('Register')
                    ->waitForText('Welcome back', 10)
                    ->assertPathIs('/volunteer/dashboard');

            // ==========================================
            // 5. VOLUNTEER JOINS EVENT
            // ==========================================
            $browser->visit('/events/' . $eventId)
                    ->waitForText('Beach Cleanup Attendance E2E', 10)
                    ->click("button[onclick*=\"openConfirmModal\"]")
                    ->pause(1000)
                    ->click("button[onclick*=\"submitRegistration\"]")
                    // Setelah register, modal sukses muncul
                    ->waitForText('successfully registered', 10)
                    ->click("button[onclick*=\"closeSuccessModal\"]");

            // ==========================================
            // 6. LOGOUT VOLUNTEER
            // ==========================================
            $browser->visit('/volunteer/dashboard')
                    ->press('Logout')
                    ->waitForText('Login', 10);

            // ==========================================
            // 7. LOGIN KEMBALI SEBAGAI ORGANIZER
            // ==========================================
            $browser->visit('/login')
                    ->type('email', 'andi_org@test.com')
                    ->type('password', 'password123')
                    ->press('Login')
                    ->waitForText('Dashboard', 10);

            // ==========================================
            // 8. ORGANIZER MARKS VOLUNTEER AS PRESENT
            // ==========================================
            $browser->visit('/events/' . $eventId . '/participants')
                    ->waitForText('Budi Volunteer', 10)
                    // Klik tombol "Present" (hitam)
                    ->click("form[action$=\"/mark\"] button.bg-black")
                    // Tunggu sampai badge "Present" muncul
                    ->waitForText('Present', 10)
                    ->assertSee('Present');
        });
    }
}
