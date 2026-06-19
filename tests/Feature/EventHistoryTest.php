<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventHistoryTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->volunteer = User::where('role', 'volunteer')->first() ?? User::factory()->create([
            'role' => 'volunteer'
        ]);
        
        $this->event = Event::factory()->create([
            'title' => 'Ocean Clean Up',
            'status' => 'published',
            'event_date' => now()->subDays(5),
        ]);
    }

    public function test_KBP115_TC05_view_event_history_list()
    {
        // Precondition: Volunteer has participated in at least 1 finished event
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        Attendance::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'present',
        ]);

        // Step: View history
        $response = $this->actingAs($this->volunteer)->get(route('events.history'));

        // Expected Result: History list shown
        $response->assertStatus(200);
        $response->assertSee('Ocean Clean Up');
    }

    public function test_KBP115_TC06_view_empty_event_history()
    {
        // Precondition: No event history
        $response = $this->actingAs($this->volunteer)->get(route('events.history'));

        // Expected Result: Belum ada riwayat event message
        $response->assertStatus(200);
        $response->assertSeeText('Belum ada riwayat event'); // Adjust as per actual text in view
    }

    public function test_KBP115_TC07_search_event_history_found()
    {
        // Precondition: Has event history
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        Attendance::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'present',
        ]);

        // Step: Search history
        $response = $this->actingAs($this->volunteer)->get(route('events.history', ['search' => 'Ocean']));

        // Expected Result: Event displayed
        $response->assertStatus(200);
        $response->assertSee('Ocean Clean Up');
    }

    public function test_KBP115_TC08_search_event_history_not_found()
    {
        // Precondition: Has event history
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        Attendance::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'present',
        ]);

        // Step: Search history not found
        $response = $this->actingAs($this->volunteer)->get(route('events.history', ['search' => 'Mountain']));

        // Expected Result: Not found message
        $response->assertStatus(200);
        $response->assertSeeText('Tidak ada event yang sesuai dengan pencarian'); // Adjust as per actual text in view
    }
}