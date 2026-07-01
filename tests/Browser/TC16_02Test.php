<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use App\Models\Documentation;
use App\Models\Certificate;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TC16_02Test extends DuskTestCase
{
    protected $volunteer;
    protected $organizer;
    protected $eventB;
    protected $docB;

    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create/Find Volunteer
        $this->volunteer = User::firstOrCreate(
            ['email' => 'volunteer2@mail.com'],
            [
                'name' => 'Volunteer 2',
                'password' => bcrypt('123456'),
                'role' => 'volunteer',
            ]
        );

        // 2. Create/Find Organizer
        $this->organizer = User::firstOrCreate(
            ['email' => 'organizer@oceancare.com'],
            [
                'name' => 'Organizer 1',
                'password' => bcrypt('123456'),
                'role' => 'organizer',
            ]
        );

        // 3. Create Event B (For TC-16-02: Absent, Documentation Approved)
        $this->eventB = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event B Cert ' . uniqid(),
            'description' => 'Beach clean-up event B',
            'location' => 'Kuta Beach',
            'event_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'duration' => 4,
            'quota' => 20,
            'status' => 'published',
        ]);
        EventRegistration::create(['event_id' => $this->eventB->id, 'user_id' => $this->volunteer->id]);
        Attendance::create(['event_id' => $this->eventB->id, 'user_id' => $this->volunteer->id, 'status' => 'absent']);
        $this->docB = Documentation::create([
            'event_id' => $this->eventB->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_cert_b.png',
            'status' => 'approved',
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up certificates files
        if ($this->volunteer) {
            $certs = Certificate::where('user_id', $this->volunteer->id)->get();
            foreach ($certs as $cert) {
                $path = str_replace('storage/', '', $cert->file_path);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
                $cert->delete();
            }
            // Clean up attendances & registrations
            Attendance::where('user_id', $this->volunteer->id)->delete();
            EventRegistration::where('user_id', $this->volunteer->id)->delete();
        }

        // Clean up events & documentations
        if ($this->eventB) {
            Documentation::where('event_id', $this->eventB->id)->delete();
            $this->eventB->delete();
        }

        parent::tearDown();
    }

    /**
     * TC-16-02: Sertifikat Tidak Dibuat Jika Volunteer Tidak Hadir
     */
    public function test_tc_16_02_no_certificate_when_absent(): void
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Login as Volunteer
            $browser->visit('/login')
                ->type('email', $this->volunteer->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/volunteer/dashboard');

            // Step 2: Open Certificates Page
            $browser->visit('/certificates')
                ->waitForText('My Certificates');

            // Assert Event B is listed and displays the failure message on the UI
            $browser->assertSee($this->eventB->title)
                ->assertSee('Sertifikat gagal dibuat karena volunteer tidak hadir');

            // Step 3: Click "Generate Certificate" for Event B via JS to avoid scroll issues
            $browser->script("document.querySelector('#event-card-{$this->eventB->id} button').click();");
            
            $browser->waitForText('Sertifikat gagal dibuat karena volunteer tidak hadir.')
                ->assertSee('Sertifikat gagal dibuat karena volunteer tidak hadir.')
                ->pause(3000); // Wait 3 seconds so the observer can see the toast at the top

            // Step 4: Trigger Generate endpoint via JS Fetch directly (to verify API level security/precondition)
            $browser->script("
                window.testFetchResult = null;
                fetch('/certificates/{$this->eventB->id}/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name=\"_token\"]').value,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json().then(data => { window.testFetchResult = { status: res.status, data: data }; }));
            ");

            // Wait for fetch callback
            $browser->waitUntil("return window.testFetchResult !== null;", 5);

            // Assert 403 Forbidden response and error message
            $result = $browser->script("return window.testFetchResult;")[0];
            $this->assertEquals(403, $result['status']);
            $this->assertFalse($result['data']['success']);
            $this->assertStringContainsString('Sertifikat gagal dibuat karena volunteer tidak hadir.', $result['data']['message']);

            // Verify database has no certificate record for Event B
            $certExists = Certificate::where('user_id', $this->volunteer->id)
                ->where('event_id', $this->eventB->id)
                ->exists();
            $this->assertFalse($certExists);
        });
    }
}
