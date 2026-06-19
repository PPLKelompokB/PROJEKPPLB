<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageEventTest extends TestCase
{
    // use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->organizer = User::where('role', 'organizer')->first() ?? User::factory()->create([
            'role' => 'organizer'
        ]);
        
        $this->event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'title' => 'DATA TESTING BARU - KBP10',
            'status' => 'published',
            'event_date' => now()->addDays(5),
        ]);
    }

    public function test_KBP10_TC01_view_events_via_dashboard()
    {
        $response = $this->actingAs($this->organizer)->get(route('organizer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('DATA TESTING BARU - KBP10');
    }

    public function test_KBP10_TC02_view_events_via_manage_event_page()
    {
        $response = $this->actingAs($this->organizer)->get(route('events.manage'));

        $response->assertStatus(200);
        $response->assertSee('DATA TESTING BARU - KBP10');
    }

    public function test_KBP10_TC03_edit_event_via_dashboard()
    {
        // Edit action is usually opening the edit page and submitting form.
        // Dashboard has edit link that goes to events.edit and then events.update
        
        $updateData = [
            'title' => 'Updated Coastal Cleanup',
            'description' => 'Updated Description',
            'date' => now()->addDays(10)->format('Y-m-d'),
            'time' => '10:00',
            'duration' => 2,
            'quota' => 100,
            'location' => 'New Location',
            'action' => 'published'
        ];

        $response = $this->actingAs($this->organizer)
                         ->put(route('events.update', $this->event->id), $updateData);

        // Usually redirects back or to manage page
        $response->assertStatus(302);
        
        $this->assertDatabaseHas('events', [
            'id' => $this->event->id,
            'title' => 'Updated Coastal Cleanup'
        ]);
    }

    public function test_KBP10_TC04_edit_event_via_manage_event_page()
    {
        // Similar to TC03 since the underlying action is the same events.update endpoint
        $updateData = [
            'title' => 'Manage Page Updated Coastal Cleanup',
            'description' => 'Updated Description 2',
            'date' => now()->addDays(15)->format('Y-m-d'),
            'time' => '10:00',
            'duration' => 2,
            'quota' => 100,
            'location' => 'New Location 2',
            'action' => 'published'
        ];

        $response = $this->actingAs($this->organizer)
                         ->put(route('events.update', $this->event->id), $updateData);

        $response->assertStatus(302);
        
        $this->assertDatabaseHas('events', [
            'id' => $this->event->id,
            'title' => 'Manage Page Updated Coastal Cleanup'
        ]);
    }

    public function test_KBP10_TC05_delete_event_via_dashboard()
    {
        $response = $this->actingAs($this->organizer)
                         ->delete(route('events.destroy', $this->event->id));

        $response->assertStatus(302);
        
        $this->assertDatabaseMissing('events', [
            'id' => $this->event->id,
        ]);
    }

    public function test_KBP10_TC06_delete_event_via_manage_event_page()
    {
        // The endpoint is the same events.destroy
        $response = $this->actingAs($this->organizer)
                         ->delete(route('events.destroy', $this->event->id));

        $response->assertStatus(302);
        
        $this->assertDatabaseMissing('events', [
            'id' => $this->event->id,
        ]);
    }
}
