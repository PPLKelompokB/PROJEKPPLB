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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class TC17_03Test extends DuskTestCase
{
    protected $volunteerA;
    protected $volunteerB;
    protected $organizer;
    protected $event;
    protected $doc;
    protected $cert;

    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create/Find Volunteer A (Active user)
        $this->volunteerA = User::firstOrCreate(
            ['email' => 'volunteer1@mail.com'],
            [
                'name' => 'Volunteer 1',
                'password' => Hash::make('123456'),
                'role' => 'volunteer',
            ]
        );

        // 2. Create/Find Volunteer B (Other user)
        $this->volunteerB = User::firstOrCreate(
            ['email' => 'volunteer2@mail.com'],
            [
                'name' => 'Volunteer 2',
                'password' => Hash::make('123456'),
                'role' => 'volunteer',
            ]
        );

        // 3. Create/Find Organizer
        $this->organizer = User::firstOrCreate(
            ['email' => 'organizer@oceancare.com'],
            [
                'name' => 'Organizer 1',
                'password' => Hash::make('123456'),
                'role' => 'organizer',
            ]
        );

        // 4. Create Event C (For TC-17-03: Owned by Volunteer B)
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event C Download ' . uniqid(),
            'description' => 'Beach clean-up event C',
            'location' => 'Nusa Dua Beach',
            'event_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'duration' => 2,
            'quota' => 20,
            'status' => 'published',
        ]);
        EventRegistration::create(['event_id' => $this->event->id, 'user_id' => $this->volunteerB->id]);
        Attendance::create(['event_id' => $this->event->id, 'user_id' => $this->volunteerB->id, 'status' => 'present']);
        $this->doc = Documentation::create([
            'event_id' => $this->event->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_dl_c.png',
            'status' => 'approved',
        ]);

        // Generate Certificate for Volunteer B in Event C
        $this->cert = Certificate::create([
            'user_id' => $this->volunteerB->id,
            'event_id' => $this->event->id,
            'file_path' => 'storage/certificates/cert_' . $this->volunteerB->id . '_' . $this->event->id . '.svg'
        ]);

        // Write the actual file on disk using CertificateController to generate the premium SVG template
        $controller = new \App\Http\Controllers\CertificateController();
        $serialNumber = 'OC-CERT-' . str_pad($this->cert->id, 6, '0', STR_PAD_LEFT);
        $svgContent = $controller->generateSvgContent(
            strtoupper($this->volunteerB->name),
            $this->event->title,
            \Carbon\Carbon::parse($this->event->event_date)->format('F d, Y'),
            $this->event->location,
            $this->event->duration,
            $serialNumber,
            $this->organizer->name
        );
        $path = 'certificates/cert_' . $this->volunteerB->id . '_' . $this->event->id . '.svg';
        Storage::disk('public')->put($path, $svgContent);
    }

    protected function tearDown(): void
    {
        // Clean up certificates files
        if ($this->cert) {
            $path = str_replace('storage/', '', $this->cert->file_path);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $this->cert->delete();
        }

        // Clean up event & documentation
        if ($this->event) {
            Documentation::where('event_id', $this->event->id)->delete();
            $this->event->delete();
        }

        // Clean up users relations
        if ($this->volunteerB) {
            Attendance::where('user_id', $this->volunteerB->id)->delete();
            EventRegistration::where('user_id', $this->volunteerB->id)->delete();
        }

        parent::tearDown();
        static::closeAll();
    }

    /**
     * TC-17-03: Volunteer Tidak Dapat Mengunduh Sertifikat Milik Volunteer Lain
     */
    public function test_tc_17_03_cannot_download_others_certificate(): void
    {
        $this->browse(function (Browser $browser) {
            // Step 1: Login as Volunteer A
            $browser->visit('/login')
                ->type('email', $this->volunteerA->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/volunteer/dashboard');

            // Step 2: Open Certificates Page
            $browser->visit('/certificates')
                ->waitForText('My Certificates');

            // Assert Event C (owned by Volunteer B) is NOT listed on Volunteer A's dashboard
            $browser->assertDontSee($this->event->title);

            // Step 3: Volunteer A attempts to fetch Volunteer B's certificate download link directly
            $browser->script("
                window.testFetchResult = null;
                fetch('/certificates/{$this->cert->id}/download')
                    .then(res => {
                        window.testFetchResult = { status: res.status };
                    })
                    .catch(err => { window.testFetchResult = { error: err.message }; });
            ");

            $browser->waitUntil("return window.testFetchResult !== null;", 10);
            $result = $browser->script("return window.testFetchResult;")[0];

            $this->assertArrayNotHasKey('error', $result);
            // System should deny access and return 403 Forbidden
            $this->assertEquals(403, $result['status']);

            // Wait until showToast is defined in the browser
            $browser->waitUntil("typeof showToast !== 'undefined'");

            // Show a visible toast notification at the top of the viewport
            $browser->script("showToast('error', 'Download sertifikat gagal: Akses ditolak (403 Forbidden).');");

            // Wait for toast to be rendered and pause so it is clearly visible to the observer
            $browser->waitForText('Download sertifikat gagal: Akses ditolak (403 Forbidden).', 5)
                ->pause(3000);
        });
    }
}
