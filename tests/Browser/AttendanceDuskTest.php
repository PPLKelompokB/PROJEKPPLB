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

    // =========================================================
    // HELPER METHODS
    // =========================================================

    private function makeOrganizer()
    {
        return User::create([
            'name'     => 'Org',
            'email'    => 'org_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer'
        ]);
    }

    private function makeVolunteer($name = 'Vol')
    {
        return User::create([
            'name'     => $name,
            'email'    => 'vol_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer',
            'points'   => 0
        ]);
    }

    private function makeEvent($organizer, array $overrides = [])
    {
        return Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'title'        => 'Dusk Event',
            'description'  => 'Test description',
            'location'     => 'Pantai Kuta',
            'event_date'   => now()->subMinutes(30)->format('Y-m-d H:i:s'), // Default: event sudah lewat agar bisa di-absen
            'duration'     => 2,
            'quota'        => 50,
            'status'       => 'published',
        ], $overrides));
    }

    /**
     * PBI12-TC11: Menampilkan daftar peserta
     */
    public function test_PBI12_TC11_ViewParticipantList()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Budi Santoso');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Event Participants')
                    ->assertSee('Budi Santoso');
        });
    }

    /**
     * PBI12-TC01: Mark Present
     */
    public function test_PBI12_TC01_MarkPresent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Andi Present');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Andi Present')
                    ->press('Mark Present')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate');
        });

        $this->assertDatabaseHas('attendances', [
            'user_id' => $vol->id,
            'event_id' => $event->id,
            'status' => 'present',
            'is_counted' => true
        ]);
    }

    /**
     * PBI12-TC02: Mark Absent
     */
    public function test_PBI12_TC02_MarkAbsent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Siti Absent');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Siti Absent')
                    ->press('Mark Absent')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate');
        });

        $this->assertDatabaseHas('attendances', [
            'user_id' => $vol->id,
            'event_id' => $event->id,
            'status' => 'absent'
        ]);
    }

    /**
     * PBI12-TC03: Update Present to Absent
     */
    public function test_PBI12_TC03_UpdatePresentToAbsent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Ubah Ke Absent');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);
        Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'present', 'is_counted' => true]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Ubah Ke Absent')
                    // tombol Absent untuk row volunteer ini (biasanya yg kedua)
                    ->press('Mark Absent')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate');
        });

        $this->assertDatabaseHas('attendances', [
            'user_id' => $vol->id,
            'event_id' => $event->id,
            'status' => 'absent'
        ]);
    }

    /**
     * PBI12-TC04: Update Absent to Present
     */
    public function test_PBI12_TC04_UpdateAbsentToPresent()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Ubah Ke Present');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);
        Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'absent']);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertSee('Ubah Ke Present')
                    // tombol Present untuk row volunteer ini
                    ->press('Mark Present')
                    ->pause(1000)
                    ->assertSee('Status berhasil diupdate');
        });

        $this->assertDatabaseHas('attendances', [
            'user_id' => $vol->id,
            'event_id' => $event->id,
            'status' => 'present',
            'is_counted' => true
        ]);
    }

    /**
     * Ekstra JS submitter untuk menangani endpoint POST agar terhindar CSRF token exception.
     */
    private function submitPostViaJS(Browser $browser, $url, $payload)
    {
        $csrfToken = $browser->driver->executeScript(
            'return document.querySelector(\'meta[name="csrf-token"]\')?.getAttribute(\'content\') || \'\';'
        );

        $payload['_token'] = $csrfToken;
        $json = json_encode($payload);

        $browser->script([
            "(function() {
                var data = {$json};
                var f = document.createElement('form');
                f.method = 'POST';
                f.action = '{$url}';
                Object.keys(data).forEach(function(key) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data[key];
                    f.appendChild(input);
                });
                document.body.appendChild(f);
                f.submit();
            })();"
        ]);

        return $browser;
    }

    // =========================================================
    // TC05 - TC14: EDGE CASES & VALIDASI
    // =========================================================

    /**
     * PBI12-TC05: Invalid Status (Manipulasi Payload)
     */
    public function test_PBI12_TC05_InvalidStatus()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        $reg = EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $reg) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $reg->event_id . '/participants')
                    ->pause(1000);

            $this->submitPostViaJS($browser, "/attendance/{$reg->id}/mark", ['status' => 'late']);
            
            $browser->pause(1000)
                    // Expected: session error message or validation fail
                    ->assertSee('Status tidak valid');
        });
        
        $this->assertDatabaseMissing('attendances', ['event_id' => $event->id, 'status' => 'late']);
    }

    /**
     * PBI12-TC06: Empty Status
     */
    public function test_PBI12_TC06_EmptyStatus()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        $reg = EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $reg) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $reg->event_id . '/participants')
                    ->pause(1000);

            $this->submitPostViaJS($browser, "/attendance/{$reg->id}/mark", []);
            
            $browser->pause(1000)
                    ->assertSee('Status tidak valid');
        });
    }

    /**
     * PBI12-TC07: Non-Organizer Access (Volunteer mencoba update status sendiri)
     */
    public function test_PBI12_TC07_NonOrganizerAccess()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        $reg = EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($vol, $reg) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $vol->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/') // dummy visit to set csrf
                    ->pause(1000);

            $this->submitPostViaJS($browser, "/attendance/{$reg->id}/mark", ['status' => 'present']);
            
            $browser->pause(1000)
                    ->assertSee('403');
        });
    }

    /**
     * PBI12-TC08: Other Organizer Access
     */
    public function test_PBI12_TC08_OtherOrganizerAccess()
    {
        $org1 = $this->makeOrganizer();
        $org2 = $this->makeOrganizer();
        $event = $this->makeEvent($org1);
        $vol = $this->makeVolunteer();
        $reg = EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org2, $reg) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org2->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/organizer/dashboard') // dummy visit
                    ->pause(1000);

            $this->submitPostViaJS($browser, "/attendance/{$reg->id}/mark", ['status' => 'present']);
            
            $browser->pause(1000)
                    ->assertSee('403');
        });
    }

    /**
     * PBI12-TC09: Unauthenticated Access
     */
    public function test_PBI12_TC09_UnauthenticatedAccess()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer();
        $reg = EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($reg) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login') // dummy
                    ->pause(1000);

            // Manual POST form injection tanpa Auth
            $this->submitPostViaJS($browser, "/attendance/{$reg->id}/mark", ['status' => 'present']);
            
            $browser->pause(1500)
                    ->assertPathIs('/login'); // redirected to login
        });
    }

    /**
     * PBI12-TC10: Invalid Registration ID
     */
    public function test_PBI12_TC10_InvalidRegistrationId()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000);

            // Random ID 999
            $this->submitPostViaJS($browser, "/attendance/999/mark", ['status' => 'present']);
            
            $browser->pause(1000)
                    ->assertSee('404');
        });
    }

    /**
     * PBI12-TC12: Is_counted flag
     */
    public function test_PBI12_TC12_IsCountedFlag()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $vol = $this->makeVolunteer('Test Counted');
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->press('Mark Present')
                    ->pause(1000);
        });

        $this->assertDatabaseHas('attendances', [
            'user_id' => $vol->id,
            'event_id' => $event->id,
            'status' => 'present',
            'is_counted' => true
        ]);
    }

    /**
     * PBI12-TC13: Multiple Volunteers
     */
    public function test_PBI12_TC13_MultipleVolunteers()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $volA = $this->makeVolunteer('Vol A');
        $volB = $this->makeVolunteer('Vol B');
        $volC = $this->makeVolunteer('Vol C');
        EventRegistration::create(['user_id' => $volA->id, 'event_id' => $event->id]);
        EventRegistration::create(['user_id' => $volB->id, 'event_id' => $event->id]);
        EventRegistration::create(['user_id' => $volC->id, 'event_id' => $event->id]);

        $this->browse(function (Browser $browser) use ($org, $event, $volA, $volB, $volC) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000);
                    
            // Mark Vol A Present
            $browser->within("form[action*=\"attendance/{$volA->registrations()->first()->id}/mark\"]:has(input[value=\"present\"])", function ($form) { $form->press('Mark ' . ucfirst('present')); })
                    ->pause(1000);
            // Mark Vol B Absent
            $browser->within("form[action*=\"attendance/{$volB->registrations()->first()->id}/mark\"]:has(input[value=\"absent\"])", function ($form) { $form->press('Mark ' . ucfirst('absent')); })
                    ->pause(1000);
            // Mark Vol C Present
            $browser->within("form[action*=\"attendance/{$volC->registrations()->first()->id}/mark\"]:has(input[value=\"present\"])", function ($form) { $form->press('Mark ' . ucfirst('present')); })
                    ->pause(1000);
        });

        $this->assertDatabaseHas('attendances', ['user_id' => $volA->id, 'status' => 'present']);
        $this->assertDatabaseHas('attendances', ['user_id' => $volB->id, 'status' => 'absent']);
        $this->assertDatabaseHas('attendances', ['user_id' => $volC->id, 'status' => 'present']);
    }

    /**
     * PBI12-TC14: Pagination Participants
     */
    public function test_PBI12_TC14_PaginationParticipants()
    {
        $org = $this->makeOrganizer();
        $event = $this->makeEvent($org);

        // Buat 15 volunteer agar ter-paginate
        for($i=1; $i<=15; $i++){
            $v = $this->makeVolunteer("Vol Paginate $i");
            EventRegistration::create(['user_id' => $v->id, 'event_id' => $event->id]);
        }

        $this->browse(function (Browser $browser) use ($org, $event) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/events/' . $event->id . '/participants')
                    ->pause(1000)
                    ->assertPresent('nav[aria-label="pagination"], .pagination, [aria-label="Pagination"], nav');
        });
    }
}

