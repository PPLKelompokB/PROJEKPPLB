<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TC13_03Test extends DuskTestCase
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

        // Ensure the invalid file exists in tests/Browser/source/
        $sourceDir = base_path('tests/Browser/source');
        if (!is_dir($sourceDir)) {
            mkdir($sourceDir, 0755, true);
        }
        file_put_contents($sourceDir . '/test_invalid.txt', 'This is a dummy text file to test validation for invalid upload documentation formats.');

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
        // Clean up invalid file
        $filePath = base_path('tests/Browser/source/test_invalid.txt');
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Clean up event
        if ($this->event) {
            $this->event->delete();
        }

        parent::tearDown();
        static::closeAll();
    }

    /**
     * TC-13-03: Upload dokumentasi gagal karena format file tidak sesuai
     */
    public function test_tc_13_03_upload_documentation_invalid_format(): void
    {
        $this->browse(function (Browser $browser) {
            $filePath = realpath(base_path('tests/Browser/source/test_invalid.txt'));

            $browser->visit('/login')
                ->type('email', $this->organizer->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/organizer/dashboard')
                ->assertPathIs('/organizer/dashboard')
                ->visit('/documentation?event_id=' . $this->event->id)
                ->waitForText('Upload Documentation');

            // Attach invalid file type (.txt instead of .png/jpg)
            $browser->attach('file', $filePath)
                ->type('note', 'Trying to upload a text file.')
                ->script("document.querySelector('form[action*=\"/documentation\"] button[type=\"submit\"]').click();");

            $browser->waitForText('The file field must be a file of type: jpg, jpeg, png.')
                ->assertSee('The file field must be a file of type: jpg, jpeg, png.')
                ->pause(3000); // Wait so observer can see the result
        });
    }
}
