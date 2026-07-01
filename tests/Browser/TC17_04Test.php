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

class TC17_04Test extends DuskTestCase
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

        // 3. Create Event A (For TC-17-04: File missing)
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

        // Generate Certificate database record but DO NOT write physical file
        $this->cert = Certificate::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'file_path' => 'storage/certificates/cert_' . $this->volunteer->id . '_' . $this->event->id . '.svg'
        ]);

        // Delete physical file if it exists by any chance
        $path = 'certificates/cert_' . $this->volunteer->id . '_' . $this->event->id . '.svg';
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function tearDown(): void
    {
        // Clean up certificates files & database record
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
     * TC-17-04: Download Sertifikat Gagal Karena File Tidak Ditemukan
     */
    public function test_tc_17_04_download_fail_file_not_found(): void
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

            // Step 3: Try to download certificate directly
            $browser->visit("/certificates/{$this->cert->id}/download")
                ->waitForText('Download sertifikat gagal karena file tidak ditemukan di server.')
                ->assertSee('Download sertifikat gagal karena file tidak ditemukan di server.')
                ->pause(3000);
        });
    }
}
