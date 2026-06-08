<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use App\Models\EventRegistration;
use App\Models\Notification;
use App\Models\Point;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDocumentationVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'admin']);
        $this->organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Bersih Pantai',
            'description' => 'Test Desc',
            'location' => 'Loc',
            'duration' => 2,
            'quota' => 10,
            'status' => 'published',
            'image' => '',
            'event_date' => now()->subDay(),
        ]);

        $this->documentation = Documentation::create([
            'event_id' => $this->event->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'doc.jpg',
            'status' => 'pending',
            'note' => 'Laporan akhir bersih pantai'
        ]);

        // Create some volunteers
        $this->volunteer1 = User::create(['name' => 'Vol1', 'email' => 'vol1'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $this->volunteer2 = User::create(['name' => 'Vol2', 'email' => 'vol2'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);

        EventRegistration::create(['user_id' => $this->volunteer1->id, 'event_id' => $this->event->id]);
        EventRegistration::create(['user_id' => $this->volunteer2->id, 'event_id' => $this->event->id]);
    }

    /** @test PBI14-TC01 & PBI14-TC15 */
    public function test_admin_can_view_documentation_list_with_pagination()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.documentation.index'));

        $response->assertStatus(200);
        $response->assertViewHas('events');
    }

    /** @test PBI14-TC02 */
    public function test_admin_can_view_documentation_detail()
    {
        $response = $this->actingAs($this->admin)
                         ->get(route('admin.documentation.show', $this->event->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.documentation.show');
    }

    /** @test PBI14-TC03, PBI14-TC10, PBI14-TC12, PBI14-TC14, PBI15-TC01 */
    public function test_admin_can_approve_documentation_and_award_points_and_send_notification()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('documentation.verify', $this->documentation->id), [
                             'status' => 'approved'
                         ]);

        $response->assertStatus(200);

        // Check documentation status
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'approved'
        ]);

        // Check points awarded (duration 2 * 10 = 20 points)
        $this->assertDatabaseHas('points', [
            'user_id' => $this->volunteer1->id,
            'points' => 20
        ]);

        $this->assertDatabaseHas('points', [
            'user_id' => $this->volunteer2->id,
            'points' => 20
        ]);

        // Check notification to organizer
        $notification = Notification::where('user_id', $this->organizer->id)->first();
        $this->assertStringContainsString('Documentation Approved', $notification->title);
        $this->assertEquals('success', $notification->type);
    }

    /** @test PBI14-TC04, PBI14-TC13, PBI15-TC02 */
    public function test_admin_can_reject_documentation_and_send_warning_notification()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('documentation.verify', $this->documentation->id), [
                             'status' => 'rejected',
                             'reject_reason' => 'Foto blur'
                         ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'rejected'
        ]);

        // Points should NOT be awarded
        $this->assertDatabaseMissing('points', [
            'user_id' => $this->volunteer1->id
        ]);

        // Check warning notification to organizer
        $notification = Notification::where('user_id', $this->organizer->id)->first();
        $this->assertStringContainsString('Documentation Rejected', $notification->title);
        $this->assertEquals('error', $notification->type);
    }

    /** @test PBI14-TC05 & PBI14-TC06 */
    public function test_invalid_or_empty_status_is_rejected_with_422()
    {
        // PBI14-TC05 Invalid Status
        $response1 = $this->actingAs($this->admin)
                          ->postJson(route('documentation.verify', $this->documentation->id), [
                              'status' => 'pending' // Asumsi hanya boleh approved / rejected
                          ]);
        $response1->assertStatus(422);

        // PBI14-TC06 Empty Status
        $response2 = $this->actingAs($this->admin)
                          ->postJson(route('documentation.verify', $this->documentation->id), []);
        $response2->assertStatus(422);
    }

    /** @test PBI14-TC07 */
    public function test_invalid_documentation_id_returns_404()
    {
        $response = $this->actingAs($this->admin)
                         ->postJson(route('documentation.verify', 999), [
                             'status' => 'approved'
                         ]);
        $response->assertStatus(404);
    }

    /** @test PBI14-TC08 */
    public function test_non_admin_cannot_verify_documentation()
    {
        $response = $this->actingAs($this->organizer)
                         ->postJson(route('documentation.verify', $this->documentation->id), [
                             'status' => 'approved'
                         ]);
        $response->assertStatus(403);
    }

    /** @test PBI14-TC09 */
    public function test_unauthenticated_user_cannot_verify_documentation()
    {
        $response = $this->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);
        $response->assertStatus(401);
    }

    /** @test PBI14-TC11 */
    public function test_no_duplicate_points_awarded()
    {
        // First approval
        $this->actingAs($this->admin)->post(route('documentation.verify', $this->documentation->id), ['status' => 'approved']);
        
        $initialPointCount = Point::count();

        // Second approval attempt
        $this->actingAs($this->admin)->post(route('documentation.verify', $this->documentation->id), ['status' => 'approved']);

        // Assert points count hasn't increased from duplicate points
        $this->assertEquals($initialPointCount, Point::count());
    }
}
