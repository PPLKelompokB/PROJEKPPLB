<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisteredEventTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup base data
        $this->volunteer = User::where('role', 'volunteer')->first() ?? User::factory()->create([
            'role' => 'volunteer'
        ]);
        
        $this->event = Event::factory()->create([
            'title' => 'Beach Cleanup Bali',
            'status' => 'published',
            'event_date' => now()->addDays(5),
        ]);
    }

    public function test_KBP115_TC01_view_registered_events_list()
    {
        // Precondition: Volunteer has an account and registered for at least 1 event
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        // Step: Login & view registered events
        $response = $this->actingAs($this->volunteer)
                         ->get(route('volunteer.registered-events'));

        // Expected Result: System displays registered event list
        $response->assertStatus(200);
        $response->assertSee('Beach Cleanup Bali');
    }

    public function test_KBP115_TC02_view_registered_events_empty()
    {
        // Precondition: Volunteer has no registered events
        
        // Step: Login & view registered events
        $response = $this->actingAs($this->volunteer)
                         ->get(route('volunteer.registered-events'));

        // Expected Result: System displays "Belum ada event yang terdaftar" (or similar empty state)
        $response->assertStatus(200);
        $response->assertSeeText('Belum ada event yang terdaftar'); // Adjust text according to actual view
    }

    public function test_KBP115_TC03_search_registered_event_found()
    {
        // Precondition: Volunteer has registered event
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        // Step: Search event
        $response = $this->actingAs($this->volunteer)
                         ->get(route('volunteer.registered-events', ['search' => 'Beach']));

        // Expected Result: Event displayed
        $response->assertStatus(200);
        $response->assertSee('Beach Cleanup Bali');
    }

    public function test_KBP115_TC04_search_registered_event_not_found()
    {
        // Precondition: Volunteer has registered event
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        // Step: Search non-existent event
        $response = $this->actingAs($this->volunteer)
                         ->get(route('volunteer.registered-events', ['search' => 'Mountain']));

        // Expected Result: Not found message
        $response->assertStatus(200);
        $response->assertSeeText('Tidak ada event yang sesuai dengan pencarian'); // Adjust text according to actual view
    }
}
