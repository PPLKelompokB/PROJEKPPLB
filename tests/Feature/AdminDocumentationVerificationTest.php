<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use App\Models\EventRegistration;
use App\Models\Notification;
use App\Models\Point;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDocumentationVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Precondition: User memiliki akun aktif dengan role Admin
        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'admin']);
        
        // Organizer
        $this->organizer = User::create(['name' => 'Org', 'email' => 'org'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        
        // Precondition: Terdapat data event yang memiliki dokumentasi (Event bernama "Bersih Pantai", duration=2 jam)
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Bersih Pantai',
            'description' => 'Test Desc',
            'location' => 'Loc',
            'duration' => 2, // duration=2 jam
            'quota' => 10,
            'status' => 'published',
            'image' => '',
            'event_date' => now()->subDay(),
        ]);

        // Precondition: Terdapat dokumentasi dengan status pending (ID 1) dan note
        $this->documentation = Documentation::create([
            'event_id' => $this->event->id,
            'organizer_id' => $this->organizer->id,
            'file_path' => 'doc.jpg',
            'status' => 'pending',
            'note' => 'Laporan akhir bersih pantai'
        ]);

        // Precondition: 3 volunteer terdaftar
        $this->volunteer1 = User::create(['name' => 'Vol1', 'email' => 'vol1'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $this->volunteer2 = User::create(['name' => 'Vol2', 'email' => 'vol2'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);
        $this->volunteer3 = User::create(['name' => 'Vol3', 'email' => 'vol3'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'volunteer']);

        EventRegistration::create(['user_id' => $this->volunteer1->id, 'event_id' => $this->event->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $this->volunteer2->id, 'event_id' => $this->event->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $this->volunteer3->id, 'event_id' => $this->event->id, 'status' => 'registered']);
        
        Attendance::create(['user_id' => $this->volunteer1->id, 'event_id' => $this->event->id, 'status' => 'present']);
        Attendance::create(['user_id' => $this->volunteer2->id, 'event_id' => $this->event->id, 'status' => 'present']);
        Attendance::create(['user_id' => $this->volunteer3->id, 'event_id' => $this->event->id, 'status' => 'present']);
    }

    public function test_PBI14_TC01_ViewDocumentationList()
    {
        // Step 1: Login sebagai admin
        // Step 2: Buka halaman /admin/documentation
        $response = $this->actingAs($this->admin)->get(route('admin.documentation.index'));
        
        // Expected Result: Halaman menampilkan daftar event yang memiliki dokumentasi... Tampil pagination 10 per halaman.
        $response->assertStatus(200);
        $response->assertViewHas('events');
    }

    public function test_PBI14_TC02_ViewDocumentationDetail()
    {
        // Step 1: Login sebagai admin
        // Step 2: Buka halaman /admin/documentation/{eventId}
        $response = $this->actingAs($this->admin)->get(route('admin.documentation.show', $this->event->id));
        
        // Expected Result: Halaman menampilkan detail event beserta file dokumentasi.
        $response->assertStatus(200);
        $response->assertViewIs('admin.documentation.show');
        $response->assertSee('Event Documentation');
    }

    public function test_PBI14_TC03_ApproveDocumentation()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: approved)
        $response = $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Response JSON berisi pesan verifikasi berhasil
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Verifikasi berhasil + notifikasi terkirim',
            'status' => 'approved'
        ]);

        // Expected Result: Status dokumentasi diupdate menjadi approved di tabel documentations
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'approved'
        ]);
        
        // Expected Result: Poin otomatis diberikan ke semua volunteer terdaftar (duration x 10)
        $this->assertDatabaseHas('points', [
            'user_id' => $this->volunteer1->id,
            'points' => 20
        ]);
    }

    public function test_PBI14_TC04_RejectDocumentation()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: rejected)
        $response = $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'rejected'
        ]);

        // Expected Result: Response JSON berisi pesan verifikasi berhasil, status rejected
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Verifikasi berhasil + notifikasi terkirim',
            'status' => 'rejected'
        ]);

        // Expected Result: Status dokumentasi diupdate menjadi rejected di tabel documentations
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'rejected'
        ]);
        
        // Expected Result: Tidak ada poin yang diberikan
        $this->assertDatabaseMissing('points', [
            'user_id' => $this->volunteer1->id
        ]);
    }

    public function test_PBI14_TC05_InvalidStatusInput()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify dengan status tidak valid (cancel)
        $response = $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'cancel'
        ]);

        // Expected Result: Response HTTP 422 Unprocessable Entity
        $response->assertStatus(422);
        
        // Expected Result: Tidak ada perubahan status di DB
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'pending'
        ]);
    }

    public function test_PBI14_TC06_EmptyStatusInput()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify tanpa field status
        $response = $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), []);

        // Expected Result: Response HTTP 422 Unprocessable Entity
        $response->assertStatus(422);
        
        // Expected Result: Tidak ada perubahan di DB
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'pending'
        ]);
    }

    public function test_PBI14_TC07_InvalidDocumentationId()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/999/verify (ID tidak ada di DB) (status: approved)
        $response = $this->actingAs($this->admin)->postJson(route('documentation.verify', 999), [
            'status' => 'approved'
        ]);

        // Expected Result: Response HTTP 404 Not Found
        $response->assertStatus(404);
    }

    public function test_PBI14_TC08_NonAdminAccess()
    {
        // Step 1: Login sebagai organizer
        // Step 2: Kirim POST request ke /documentation/1/verify (status: approved)
        $response = $this->actingAs($this->organizer)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Response HTTP 403 Forbidden
        $response->assertStatus(403);
        
        // Expected Result: Status dokumentasi tidak berubah
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'pending'
        ]);
    }

    public function test_PBI14_TC09_UnauthenticatedAccess()
    {
        // Step 1: Kirim POST request ke /documentation/1/verify tanpa sesi login (status: approved)
        $response = $this->post(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Redirect ke halaman login (HTTP 302)
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // Expected Result: Tidak ada perubahan di DB
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'pending'
        ]);
    }

    public function test_PBI14_TC10_PointCalculationApproval()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: approved)
        $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Masing-masing volunteer mendapat record poin sebesar 20 (2 x 10)
        $this->assertDatabaseHas('points', ['user_id' => $this->volunteer1->id, 'points' => 20]);
        $this->assertDatabaseHas('points', ['user_id' => $this->volunteer2->id, 'points' => 20]);
        $this->assertDatabaseHas('points', ['user_id' => $this->volunteer3->id, 'points' => 20]);

        // Expected Result: Field users.points masing-masing bertambah 20
        $this->assertEquals(20, $this->volunteer1->fresh()->points);
        $this->assertEquals(20, $this->volunteer2->fresh()->points);
        $this->assertEquals(20, $this->volunteer3->fresh()->points);
    }

    public function test_PBI14_TC11_NoDuplicatePoints()
    {
        // Precondition: Terdapat dokumentasi yang sebelumnya sudah di-approve
        $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        $initialPointCount = Point::count();

        // Kembalikan status ke pending untuk mensimulasikan persetujuan ulang
        $this->documentation->update(['status' => 'pending']);

        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: approved)
        $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Sistem mengecek poin. Tabel points tetap memiliki 1 record per volunteer per event
        $this->assertEquals($initialPointCount, Point::count());
    }

    public function test_PBI14_TC12_NotificationApproved()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: approved)
        $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Notifikasi tersimpan dengan title="Documentation Approved: Bersih Pan..." dan type="success"
        $notification = Notification::where('user_id', $this->organizer->id)->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Documentation Approved: Bersih Pan', $notification->title);
        $this->assertEquals('success', $notification->type);
        $this->assertFalse((bool)$notification->is_read);
    }

    public function test_PBI14_TC13_NotificationRejected()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: rejected)
        $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'rejected'
        ]);

        // Expected Result: Notifikasi tersimpan dengan title="Documentation Rejected: Bersih Pan..." dan type="error"
        $notification = Notification::where('user_id', $this->organizer->id)->first();
        $this->assertNotNull($notification);
        $this->assertStringContainsString('Documentation Rejected: Bersih Pan', $notification->title);
        $this->assertEquals('error', $notification->type);
        $this->assertFalse((bool)$notification->is_read);
    }

    public function test_PBI14_TC14_ApproveWithNote()
    {
        // Step 1: Login sebagai admin
        // Step 2: Kirim POST request ke /documentation/1/verify (status: approved)
        $this->actingAs($this->admin)->postJson(route('documentation.verify', $this->documentation->id), [
            'status' => 'approved'
        ]);

        // Expected Result: Status berubah approved
        $this->assertDatabaseHas('documentations', [
            'id' => $this->documentation->id,
            'status' => 'approved'
        ]);

        // Expected Result: Notifikasi mencantumkan cuplikan note
        $notification = Notification::where('user_id', $this->organizer->id)->first();
        $this->assertStringContainsString('Laporan akhir bersih pantai', $notification->message);
        
        // Expected Result: Poin diberikan ke volunteer
        $this->assertDatabaseHas('points', [
            'user_id' => $this->volunteer1->id,
            'points' => 20
        ]);
    }

    public function test_PBI14_TC15_PaginationDocumentationList()
    {
        // Precondition: Terdapat lebih dari 10 event dengan dokumentasi
        for ($i = 0; $i < 15; $i++) {
            $evt = Event::create([
                'organizer_id' => $this->organizer->id,
                'title' => 'Event ' . $i,
                'description' => 'Desc',
                'location' => 'Loc',
                'duration' => 2,
                'quota' => 10,
                'status' => 'published',
                'image' => '',
                'event_date' => now()->subDay(),
            ]);
            Documentation::create([
                'event_id' => $evt->id,
                'organizer_id' => $this->organizer->id,
                'file_path' => "doc$i.jpg",
                'status' => 'pending'
            ]);
        }

        // Step 1: Login sebagai admin
        // Step 2: Buka halaman /admin/documentation
        $response = $this->actingAs($this->admin)->get(route('admin.documentation.index'));
        
        // Expected Result: Daftar tampil dengan pagination 10 per halaman
        $response->assertStatus(200);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $response->original->getData()['events']);
    }
}
