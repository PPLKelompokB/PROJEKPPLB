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

class TC16_03Test extends DuskTestCase
{
    protected $volunteer;
    protected $organizer;
    protected $eventC;
    protected $docC;

    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create/Find Volunteer
        $this->volunteer = User::firstOrCreate(
            ['email' => 'volunteer3@mail.com'],
            [
                'name' => 'Volunteer 3',
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

        // 3. Create Event C (For TC-16-03: Present, Documentation Pending)
        $this->eventC = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event C Cert ' . uniqid(),
            'description' => 'Beach clean-up event C',
            'location' => 'Nusa Dua Beach',
            'event_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 20,
            'status' => 'published',
        ]);
        EventRegistration::create(['event_id' => $this->eventC->id, 'user_id' => $this->volunteer->id]);
        Attendance::create(['event_id' => $this->eventC->id, 'user_id' => $this->volunteer->id, 'status' => 'present']);
        $this->docC = Documentation::create([
            'event_id' => $this->eventC->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_cert_c.png',
            'status' => 'pending', // Pending, not approved
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
        if ($this->eventC) {
            Documentation::where('event_id', $this->eventC->id)->delete();
            $this->eventC->delete();
        }

        parent::tearDown();
    }

    /**
     * TC-16-03: Sertifikat Tidak Dibuat Jika Dokumentasi Belum Di Verifikasi
     */
    public function test_tc_16_03_no_certificate_when_documentation_pending(): void
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
                ->waitForText('My Certificates')
                // Event C should be listed since volunteer was present
                ->assertSee($this->eventC->title);

            // Step 3: Click "Generate Certificate" for Event C via JS to avoid scroll issues
            $browser->script("document.querySelector('#event-card-{$this->eventC->id} button').click();");
            
            // Wait for error toast notification
            $browser->waitForText('Sertifikat gagal dibuat karena dokumentasi belum diverifikasi.')
                ->assertSee('Sertifikat gagal dibuat karena dokumentasi belum diverifikasi.')
                ->pause(3000); // Wait 3 seconds so the observer can see the toast at the top

            // Verify database has no certificate record for Event C
            $certExists = Certificate::where('user_id', $this->volunteer->id)
                ->where('event_id', $this->eventC->id)
                ->exists();
            $this->assertFalse($certExists);
        });
    }
}
