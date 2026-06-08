<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->volunteer = User::factory()->create(['role' => 'volunteer', 'points' => 0]);
        
        $this->event = Event::factory()->create([
            'title' => 'Santa Monica Beach Clean-Up',
            'status' => 'published',
            'event_date' => now()->subDays(5),
        ]);

        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);
    }

    public function test_HIS_TC01_volunteer_can_view_history_list()
    {
        Attendance::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'present',
        ]);

        $response = $this->actingAs($this->volunteer)->get('/history');

        $response->assertStatus(200);
        $response->assertSee('Event History');
        $response->assertSee('Santa Monica Beach Clean-Up');
    }

    public function test_HIS_TC02_shows_present_status_and_points_earned()
    {
        Attendance::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id,
            'status' => 'present',
            'is_counted' => true,
        ]);
        
        $this->volunteer->update(['points' => 10]);

        $response = $this->actingAs($this->volunteer)->get('/history');

        $response->assertStatus(200);
        $response->assertSee('Present');
    }

    public function test_HIS_TC03_shows_absent_status_and_zero_points()
    {
        $absentEvent = Event::factory()->create([
            'title' => 'Suramadu View Point Restoration',
            'event_date' => now()->subDays(5)
        ]);
        
        EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $absentEvent->id,
            'status' => 'registered'
        ]);

        Attendance::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $absentEvent->id,
            'status' => 'absent',
            'is_counted' => false,
        ]);

        $response = $this->actingAs($this->volunteer)->get('/history');

        $response->assertStatus(200);
        $response->assertSee('Suramadu View Point Restoration');
        $response->assertSee('Absent');
        $response->assertSee('0 Points');
    }

    public function test_HIS_TC04_search_event_found()
    {
        $response = $this->actingAs($this->volunteer)->get('/history?search=sura&year=all');
        $response->assertStatus(200);
    }

    public function test_HIS_TC05_search_event_not_found()
    {
        $response = $this->actingAs($this->volunteer)->get('/history?search=Pantai+Kuta');

        $response->assertStatus(200);
        $response->assertSee('Belum Ada History'); 
    }

    public function test_HIS_TC06_filter_by_year()
    {
        $response = $this->actingAs($this->volunteer)->get('/history?year=2025');
        $response->assertStatus(200);
    }

    public function test_HIS_TC08_unauthenticated_user_cannot_access_history()
    {
        $response = $this->get('/history');
        $response->assertRedirect('/login');
        $response->assertStatus(302);
    }
}