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

class TC17_01Test extends DuskTestCase
{
    protected $volunteer;
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

        // 1. Create/Find Volunteer
        $this->volunteer = User::firstOrCreate(
            ['email' => 'volunteer1@mail.com'],
            [
                'name' => 'Volunteer 1',
                'password' => Hash::make('123456'),
                'role' => 'volunteer',
            ]
        );

        // 2. Create/Find Organizer
        $this->organizer = User::firstOrCreate(
            ['email' => 'organizer@oceancare.com'],
            [
                'name' => 'Organizer 1',
                'password' => Hash::make('123456'),
                'role' => 'organizer',
            ]
        );

        // 3. Create Event A (For TC-17-01: Valid Certificate & File Available)
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event A Download ' . uniqid(),
            'description' => 'Beach clean-up event A',
            'location' => 'Sanur Beach',
            'event_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'duration' => 3,
            'quota' => 20,
            'status' => 'published',
        ]);
        EventRegistration::create(['event_id' => $this->event->id, 'user_id' => $this->volunteer->id]);
        Attendance::create(['event_id' => $this->event->id, 'user_id' => $this->volunteer->id, 'status' => 'present']);
        $this->doc = Documentation::create([
            'event_id' => $this->event->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_dl_a.png',
            'status' => 'approved',
        ]);

        // Generate valid Certificate
        $this->cert = Certificate::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'file_path' => 'storage/certificates/cert_' . $this->volunteer->id . '_' . $this->event->id . '.svg'
        ]);

        // Write the actual file on disk using CertificateController to generate the premium SVG template
        $controller = new \App\Http\Controllers\CertificateController();
        $serialNumber = 'OC-CERT-' . str_pad($this->cert->id, 6, '0', STR_PAD_LEFT);
        $svgContent = $controller->generateSvgContent(
            strtoupper($this->volunteer->name),
            $this->event->title,
            \Carbon\Carbon::parse($this->event->event_date)->format('F d, Y'),
            $this->event->location,
            $this->event->duration,
            $serialNumber,
            $this->organizer->name
        );
        $path = 'certificates/cert_' . $this->volunteer->id . '_' . $this->event->id . '.svg';
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

        // Clean up attendances & registrations
        if ($this->volunteer) {
            Attendance::where('user_id', $this->volunteer->id)->delete();
            EventRegistration::where('user_id', $this->volunteer->id)->delete();
        }

        parent::tearDown();
        static::closeAll();
    }

    /**
     * TC-17-01: Download Sertifikat Berhasil
     */
    public function test_tc_17_01_download_certificate_success(): void
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
                ->assertSee($this->event->title)
                ->assertSee('Download Certificate');

            // Step 3: Open print/preview page directly and check if certificate details are shown
            $browser->visit("/certificates/{$this->cert->id}/preview")
                ->assertSee('OCEANCARE')
                ->assertSee(strtoupper($this->volunteer->name))
                ->assertSee($this->event->title)
                ->pause(3000); // Wait so observer can see preview

            // Step 5: Trigger download via Fetch in browser context to assert file response directly
            $browser->script("
                window.testFetchResult = null;
                fetch('/certificates/{$this->cert->id}/download')
                    .then(res => {
                        const disp = res.headers.get('content-disposition') || '';
                        res.text().then(text => {
                            window.testFetchResult = {
                                status: res.status,
                                contentType: res.headers.get('content-type') || '',
                                contentDisposition: disp,
                                isSvg: text.includes('<svg')
                            };
                        });
                    })
                    .catch(err => { window.testFetchResult = { error: err.message }; });
            ");

            $browser->waitUntil("return window.testFetchResult !== null;", 10);
            $result = $browser->script("return window.testFetchResult;")[0];

            $this->assertArrayNotHasKey('error', $result);
            $this->assertEquals(200, $result['status']);
            $this->assertTrue($result['isSvg'], 'Downloaded file content should be a valid SVG');
            
            // Expected filename: sertifikat_{event_id}.svg
            $expectedFilename = "sertifikat_{$this->event->id}.svg";
            $this->assertStringContainsString('attachment', $result['contentDisposition']);
            $this->assertStringContainsString($expectedFilename, $result['contentDisposition']);

            $browser->pause(3000); // Pause to keep browser visible
        });
    }
}
