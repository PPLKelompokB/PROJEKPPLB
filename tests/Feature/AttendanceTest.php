<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup base data
        $this->organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Test',
            'description' => 'Test Desc',
            'location' => 'Loc',
            'duration' => 2,
            'quota' => 10,
            'status' => 'published',
            'image' => '',
            'event_date' => now()->subDay(),
        ]);
        $this->volunteer = User::create(['name' => 'Vol', 'email' => 'vol'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $this->registration = EventRegistration::create([
            'user_id' => $this->volunteer->id,
            'event_id' => $this->event->id
        ]);
    }

    /** @test PBI12-TC01 & PBI12-TC12 */
    public function test_organizer_can_mark_volunteer_as_present()
    {
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->registration->id), [
                             'status' => 'present'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status berhasil diupdate');
        
        $this->assertDatabaseHas('attendances', [
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'status' => 'present',
            'is_counted' => true
        ]);
    }

    /** @test PBI12-TC02 */
    public function test_organizer_can_mark_volunteer_as_absent()
    {
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->registration->id), [
                             'status' => 'absent'
                         ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('attendances', [
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'status' => 'absent'
        ]);
    }

    /** @test PBI12-TC03 */
    public function test_organizer_can_update_present_to_absent()
    {
        Attendance::create([
            'event_id' => $this->event->id,
            'user_id' => $this->volunteer->id,
            'status' => 'present',
            'is_counted' => true
        ]);

        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->registration->id), [
                             'status' => 'absent'
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->volunteer->id,
            'status' => 'absent'
        ]);
    }

    /** @test PBI12-TC05 */
    public function test_invalid_status_is_rejected()
    {
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->registration->id), [
                             'status' => 'late'
                         ]);

        $response->assertSessionHas('error');
    }

    /** @test PBI12-TC06 */
    public function test_empty_status_is_rejected()
    {
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->registration->id), []);

        $response->assertSessionHas('error');
    }

    /** @test PBI12-TC07 */
    public function test_volunteer_cannot_mark_attendance()
    {
        $response = $this->actingAs($this->volunteer)
                         ->post(route('attendance.mark', $this->registration->id), [
                             'status' => 'present'
                         ]);

        $response->assertStatus(403);
    }

    /** @test PBI12-TC08 */
    public function test_other_organizer_cannot_mark_attendance()
    {
        $otherOrganizer = User::create(['name' => 'Org2', 'email' => 'org2'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        
        $response = $this->actingAs($otherOrganizer)
                         ->post(route('attendance.mark', $this->registration->id), [
                             'status' => 'present'
                         ]);

        $response->assertStatus(403);
    }

    /** @test PBI12-TC09 */
    public function test_unauthenticated_user_is_redirected()
    {
        $response = $this->post(route('attendance.mark', $this->registration->id), [
            'status' => 'present'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    /** @test PBI12-TC10 */
    public function test_marking_invalid_registration_returns_404()
    {
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', 999), [
                             'status' => 'present'
                         ]);

        $response->assertStatus(404);
    }

    /** @test PBI12-TC11 & PBI12-TC14 */
    public function test_organizer_can_view_participant_list_with_pagination()
    {
        // Add more volunteers to trigger pagination
        for ($i = 0; $i < 10; $i++) {
            $user = User::create(['name' => 'Vol'.$i, 'email' => 'vol'.$i.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
            EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $this->event->id
            ]);
        }

        $response = $this->actingAs($this->organizer)
                         ->get(route('events.participants', $this->event->id));

        $response->assertStatus(200);
        $response->assertViewHas('participants');
    }
}
