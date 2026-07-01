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

class TC16_01Test extends DuskTestCase
{
    protected $volunteer;
    protected $organizer;
    protected $eventA;
    protected $docA;

    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create/Find Volunteer
        $this->volunteer = User::firstOrCreate(
            ['email' => 'volunteer1@mail.com'],
            [
                'name' => 'Volunteer 1',
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

        // 3. Create Event A (For TC-16-01: Present, Documentation Approved)
        $this->eventA = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event A Cert ' . uniqid(),
            'description' => 'Beach clean-up event A',
            'location' => 'Sanur Beach',
            'event_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'duration' => 3,
            'quota' => 20,
            'status' => 'published',
        ]);
        EventRegistration::create(['event_id' => $this->eventA->id, 'user_id' => $this->volunteer->id]);
        Attendance::create(['event_id' => $this->eventA->id, 'user_id' => $this->volunteer->id, 'status' => 'present']);
        $this->docA = Documentation::create([
            'event_id' => $this->eventA->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_cert_a.png',
            'status' => 'approved',
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up certificates files
        /*
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
        if ($this->eventA) {
            Documentation::where('event_id', $this->eventA->id)->delete();
            $this->eventA->delete();
        }
        */

        parent::tearDown();
    }

    /**
     * TC-16-01: Generate Sertifikat Digital Berhasil
     */
    public function test_tc_16_01_generate_certificate_success(): void
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
                ->assertSee($this->eventA->title);

            // Step 3: Click "Generate Certificate" for Event A via JS to avoid scroll issues
            $browser->script("document.querySelector('#event-card-{$this->eventA->id} button').click();");
            
            $browser->waitForText('Sertifikat digital berhasil dibuat!', 15) // Wait for success toast
                ->pause(2000) // Wait for page reload to complete
                ->waitForTextIn("#event-card-{$this->eventA->id}", 'Download Certificate', 10);

            // Verify certificate is stored in database
            $cert = Certificate::where('user_id', $this->volunteer->id)
                ->where('event_id', $this->eventA->id)
                ->first();
            $this->assertNotNull($cert);
            $this->assertStringContainsString('storage/certificates/', $cert->file_path);

            // Verify file exists on disk
            $filePath = str_replace('storage/', '', $cert->file_path);
            $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath));

            // Volunteer can view the print/preview page without error
            $browser->visit("/certificates/{$cert->id}/preview")
                ->assertSee('OCEANCARE')
                ->assertSee(strtoupper($this->volunteer->name))
                ->assertSee($this->eventA->title)
                ->pause(3000); // Wait 3 seconds so the observer can see the generated certificate preview
        });
    }
}
