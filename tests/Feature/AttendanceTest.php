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
        
        // Setup Users
        $this->organizer = User::create([
            'name' => 'Organizer',
            'email' => 'organizer@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $this->otherOrganizer = User::create([
            'name' => 'Other Organizer',
            'email' => 'otherorg@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);

        $this->volunteer1 = User::create([
            'name' => 'Volunteer A',
            'email' => 'vola@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);

        $this->volunteer2 = User::create([
            'name' => 'Volunteer B',
            'email' => 'volb@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);

        $this->volunteer3 = User::create([
            'name' => 'Volunteer C',
            'email' => 'volc@test.com',
            'password' => bcrypt('password'),
            'role' => 'volunteer'
        ]);

        // Setup Event (Selesai)
        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Event Selesai',
            'description' => 'Test event yang sudah selesai',
            'location' => 'Pantai',
            'event_date' => now()->subDays(2),
            'duration' => 3,
            'quota' => 20,
            'status' => 'published'
        ]);

        // Setup Event Registrations
        $this->reg1 = EventRegistration::create([
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        $this->reg2 = EventRegistration::create([
            'user_id' => $this->volunteer2->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);

        $this->reg3 = EventRegistration::create([
            'user_id' => $this->volunteer3->id,
            'event_id' => $this->event->id,
            'status' => 'registered'
        ]);
    }

    public function test_PBI12_TC01_MarkPresent()
    {
        // Step 1: Organizer login
        // Step 2: Buka halaman peserta event selesai (event_id=1) -> klik "Hadir" pada volunteer (registration_id=1), status=present
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'present'
                         ]);

        // Expected Result: Record tersimpan di tabel attendances dengan status=present.
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'present'
        ]);

        // Expected Result: Flash message: "Status berhasil diupdate". Redirect kembali ke daftar peserta.
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status berhasil diupdate');
    }

    public function test_PBI12_TC02_MarkAbsent()
    {
        // Step 1: Organizer login
        // Step 2: klik "Tidak Hadir" pada volunteer (registration_id=1), status=absent
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'absent'
                         ]);

        // Expected Result: Record tersimpan dengan status=absent.
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'absent'
        ]);

        // Expected Result: Flash message: "Status berhasil diupdate". Redirect ke daftar peserta.
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status berhasil diupdate');
    }

    public function test_PBI12_TC03_UpdatePresentToAbsent()
    {
        // Precondition: Volunteer sudah berstatus present
        Attendance::create([
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'present'
        ]);

        // Step: organizer mengubah ke absent (registration_id=1)
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'absent'
                         ]);

        // Expected Result: Sistem meng-update record attendance (updateOrCreate) menjadi absent.
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'absent'
        ]);

        // Expected Result: Flash message: "Status berhasil diupdate".
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status berhasil diupdate');
    }

    public function test_PBI12_TC04_UpdateAbsentToPresent()
    {
        // Precondition: Volunteer sudah berstatus absent
        Attendance::create([
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'absent',
            'is_counted' => false
        ]);

        // Step: organizer mengubah ke present
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'present'
                         ]);

        // Expected Result: Record diupdate ke present, field is_counted diset true.
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'present',
            'is_counted' => true
        ]);

        // Expected Result: Flash message: "Status berhasil diupdate".
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status berhasil diupdate');
    }

    public function test_PBI12_TC05_InvalidStatus()
    {
        // Step: Organizer mengirim POST /attendance/1/mark dengan status=late (tidak valid)
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'late'
                         ]);

        // Expected Result: Redirect back. Flash error: "Status tidak valid".
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Status tidak valid');

        // Expected Result: Tidak ada perubahan di DB.
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
        ]);
    }

    public function test_PBI12_TC06_EmptyStatus()
    {
        // Step: Organizer mengirim POST /attendance/1/mark tanpa field status (null)
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', $this->reg1->id), []);

        // Expected Result: Redirect back. Flash error: "Status tidak valid".
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Status tidak valid');

        // Expected Result: Tidak ada perubahan di DB.
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
        ]);
    }

    public function test_PBI12_TC07_NonOrganizerAccess()
    {
        // Step: User login sebagai volunteer (bukan organizer) -> POST /attendance/1/mark, status=present
        $response = $this->actingAs($this->volunteer1)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'present'
                         ]);

        // Expected Result: HTTP 403 Forbidden (middleware role:organizer menolak).
        $response->assertStatus(403);

        // Expected Result: Tidak ada perubahan di DB.
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
        ]);
    }

    public function test_PBI12_TC08_OtherOrganizerAccess()
    {
        // Step: Organizer lain (bukan pemilik event) -> POST /attendance/1/mark, status=present
        $response = $this->actingAs($this->otherOrganizer)
                         ->post(route('attendance.mark', $this->reg1->id), [
                             'status' => 'present'
                         ]);

        // Expected Result: HTTP 403 Forbidden (karena event->organizer_id !== auth()->id()).
        $response->assertStatus(403);

        // Expected Result: Tidak ada perubahan di DB.
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
        ]);
    }

    public function test_PBI12_TC09_UnauthenticatedAccess()
    {
        // Step: User belum login -> POST /attendance/1/mark, status=present
        $response = $this->post(route('attendance.mark', $this->reg1->id), [
            'status' => 'present'
        ]);

        // Expected Result: Redirect ke halaman login (HTTP 302).
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // Expected Result: Tidak ada perubahan di DB.
        $this->assertDatabaseMissing('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
        ]);
    }

    public function test_PBI12_TC10_InvalidRegistrationId()
    {
        // Step: Organizer login -> POST /attendance/999/mark, registration_id tidak ada di DB
        $response = $this->actingAs($this->organizer)
                         ->post(route('attendance.mark', 999), [
                             'status' => 'present'
                         ]);

        // Expected Result: HTTP 404 Not Found (findOrFail gagal).
        $response->assertStatus(404);
    }

    public function test_PBI12_TC11_ViewParticipantList()
    {
        // Step: Organizer login -> buka /events/{id}/participants untuk event selesai
        $response = $this->actingAs($this->organizer)
                         ->get(route('events.participants', $this->event->id));

        // Expected Result: Daftar peserta tampil beserta status kehadiran.
        $response->assertStatus(200);
        $response->assertViewHas('participants');
    }

    public function test_PBI12_TC12_IsCountedFlag()
    {
        // Step: Organizer menandai volunteer sebagai present untuk pertama kali
        $this->actingAs($this->organizer)
             ->post(route('attendance.mark', $this->reg1->id), [
                 'status' => 'present'
             ]);

        // Expected Result: Attendance tersimpan dengan is_counted = true.
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->volunteer1->id,
            'event_id' => $this->event->id,
            'status' => 'present',
            'is_counted' => true
        ]);
    }

    public function test_PBI12_TC13_MultipleVolunteers()
    {
        // Precondition: Event memiliki 3 volunteer
        // Step: Volunteer A=present, B=absent, C=present secara berurutan
        $this->actingAs($this->organizer)->post(route('attendance.mark', $this->reg1->id), ['status' => 'present'])->assertSessionHas('success');
        $this->actingAs($this->organizer)->post(route('attendance.mark', $this->reg2->id), ['status' => 'absent'])->assertSessionHas('success');
        $this->actingAs($this->organizer)->post(route('attendance.mark', $this->reg3->id), ['status' => 'present'])->assertSessionHas('success');

        // Expected Result: 3 record attendance tersimpan dengan status masing-masing.
        $this->assertDatabaseHas('attendances', ['user_id' => $this->volunteer1->id, 'status' => 'present']);
        $this->assertDatabaseHas('attendances', ['user_id' => $this->volunteer2->id, 'status' => 'absent']);
        $this->assertDatabaseHas('attendances', ['user_id' => $this->volunteer3->id, 'status' => 'present']);
    }

    public function test_PBI12_TC14_PaginationParticipants()
    {
        // Precondition: Event memiliki >5 volunteer terdaftar
        for ($i = 0; $i < 6; $i++) {
            $vol = User::create([
                'name' => "Extra Vol $i",
                'email' => "extravol$i@test.com",
                'password' => bcrypt('password'),
                'role' => 'volunteer'
            ]);
            EventRegistration::create([
                'user_id' => $vol->id,
                'event_id' => $this->event->id,
                'status' => 'registered'
            ]);
        }

        // Step: organizer buka halaman daftar peserta
        $response = $this->actingAs($this->organizer)
                         ->get(route('events.participants', $this->event->id));

        // Expected Result: Peserta tampil dengan pagination 5 per halaman, navigasi halaman tersedia.
        $response->assertStatus(200);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $response->original->getData()['participants']);
        // Verify pagination per page is 5
        $this->assertEquals(5, $response->original->getData()['participants']->perPage());
    }
}
