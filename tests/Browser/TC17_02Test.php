<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use App\Models\Documentation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Hash;

class TC17_02Test extends DuskTestCase
{
    protected $volunteer;
    protected $organizer;
    protected $event;
    protected $doc;

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

        // 3. Create Event B (For TC-17-02: Certificate Not Generated)
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event B Download ' . uniqid(),
            'description' => 'Beach clean-up event B',
            'location' => 'Kuta Beach',
            'event_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'duration' => 4,
            'quota' => 20,
            'status' => 'published',
        ]);
        EventRegistration::create(['event_id' => $this->event->id, 'user_id' => $this->volunteer->id]);
        Attendance::create(['event_id' => $this->event->id, 'user_id' => $this->volunteer->id, 'status' => 'present']);
        $this->doc = Documentation::create([
            'event_id' => $this->event->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'documentations/test_dl_b.png',
            'status' => 'pending', // Pending, not approved
        ]);
    }

    protected function tearDown(): void
    {
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
     * TC-17-02: Download Sertifikat Gagal Karena Sertifikat Belum Tersedia
     */
    public function test_tc_17_02_download_fail_not_generated(): void
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
                ->assertSee($this->event->title);

            // Verify "Download Certificate" button does not exist for Event B (instead, it shows "Generate Certificate")
            $browser->assertPresent("#event-card-{$this->event->id}")
                ->assertDontSeeIn("#event-card-{$this->event->id}", 'Download Certificate');

            // Step 3: Try to download certificate directly
            $browser->visit('/certificates/999999/download')
                ->waitForText('Download sertifikat gagal karena sertifikat belum di-generate.')
                ->assertSee('Download sertifikat gagal karena sertifikat belum di-generate.')
                ->pause(3000);
        });
    }
}
