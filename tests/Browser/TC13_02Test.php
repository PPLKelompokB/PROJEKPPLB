<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TC13_02Test extends DuskTestCase
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
        // Clean up event
        if ($this->event) {
            $this->event->delete();
        }

        parent::tearDown();
        static::closeAll();
    }

    /**
     * TC-13-02: Upload dokumentasi gagal karena file tidak dilampirkan
     */
    public function test_tc_13_02_upload_documentation_without_file(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', $this->organizer->email)
                ->type('password', '123456')
                ->press('Login')
                ->waitForLocation('/organizer/dashboard')
                ->assertPathIs('/organizer/dashboard')
                ->visit('/documentation?event_id=' . $this->event->id)
                ->waitForText('Upload Documentation');

            // Remove required attribute from file input to bypass frontend validation
            $browser->script("document.querySelector('input[name=\"file\"]').removeAttribute('required');");

            // Click submit button via JS click
            $browser->script("document.querySelector('form[action*=\"/documentation\"] button[type=\"submit\"]').click();");

            $browser->waitForText('The file field is required.')
                ->assertSee('The file field is required.')
                ->pause(3000); // Wait so observer can see the result
        });
    }
}
