<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TC13_01Test extends DuskTestCase
{
    protected $organizer;
    protected $event;

    protected function hasHeadlessDisabled(): bool
    {
        return true;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create/Find Organizer User
        $this->organizer = User::firstOrCreate(
            ['email' => 'organizer@oceancare.com'],
            [
                'name' => 'Organizer 1',
                'password' => bcrypt('123456'),
                'role' => 'organizer',
            ]
        );

        // Create Event for the Organizer
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Dusk Beach Cleanup ' . uniqid(),
            'description' => 'Cleanup event for testing',
            'location' => 'Kuta Beach',
            'event_date' => now()->addDays(5)->toDateTimeString(),
            'duration' => 4,
            'quota' => 50,
            'status' => 'published',
        ]);
    }

    protected function tearDown(): void
    {
        // Clean up created documentations and files from storage
        /*
        if ($this->organizer) {
            $docs = Documentation::where('organizer_id', $this->organizer->id)->get();
            foreach ($docs as $doc) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($doc->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($doc->file_path);
                }
                $doc->delete();
            }
        }

        // Clean up event
        if ($this->event) {
            $this->event->delete();
        }
        */

        parent::tearDown();
        static::closeAll();
    }

    /**
     * TC-13-01: Upload dokumentasi berhasil dilakukan oleh Organizer dengan input valid
     */
    public function test_tc_13_01_upload_documentation_success(): void
    {
        $this->browse(function (Browser $browser) {
            $filePath = realpath(base_path('tests/Browser/source/test_image.png'));

            $browser->visit('/login')
                ->type('email', $this->organizer->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/organizer/dashboard')
                ->assertPathIs('/organizer/dashboard')
                ->visit('/documentation?event_id=' . $this->event->id)
                ->waitForText('Upload Documentation')
                ->attach('file', $filePath)
                ->pause(1000)
                ->type('textarea[name="note"]', 'This is a documentation note.')
                ->pause(1000)
                ->script("document.querySelector('form[action*=\"/documentation\"] button[type=\"submit\"]').click();");

            $browser->waitForText('Documentation uploaded successfully.')
                ->assertSee('Documentation uploaded successfully.')
                ->pause(3000); // Wait so observer can see the result

            // Verify data is saved in database
            $documentation = Documentation::where('event_id', $this->event->id)
                ->where('organizer_id', $this->organizer->id)
                ->first();

            $this->assertNotNull($documentation);
            $this->assertEquals('pending', $documentation->status);
            $this->assertEquals('This is a documentation note.', $documentation->note);
        });
    }
}
