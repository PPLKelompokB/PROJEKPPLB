<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\User;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ManageEventTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function makeOrganizer(string $suffix = ''): User
    {
        return User::create([
            'name'     => 'Test Organizer' . $suffix,
            'email'    => 'organizer' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);
    }

    private function makeEvent(User $organizer, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'title'        => 'Test Event ' . uniqid(),
            'description'  => 'Test description for event.',
            'location'     => 'Pantai Kuta, Bali',
            'event_date'   => now()->addDays(10)->format('Y-m-d H:i:s'),
            'duration'     => 3,
            'quota'        => 50,
            'status'       => 'published',
            'meeting_point' => 'Main Gate',
        ], $overrides));
    }

    private function fillEventForm(Browser $browser, array $data = []): void
    {
        $date = $data['date'] ?? now()->addDays(15)->format('Y-m-d');

        $browser->type('title', $data['title'] ?? 'Event Manajemen Test')
                ->type('location', $data['location'] ?? 'Pantai Sanur, Bali')
                ->script([
                    "document.querySelector('input[name=date]').value = '{$date}';",
                    "document.querySelector('input[name=time]').value = '09:00';"
                ]);

        $browser->select('duration', $data['duration'] ?? '3')
                ->type('quota', $data['quota'] ?? '40')
                ->type('description', $data['description'] ?? 'Deskripsi event untuk keperluan automated testing.');
    }

    // ─── TC01: View Event List ────────────────────────────────────────────────

    /**
     * PBI08-TC01-ViewEventList
     * Organizer dapat melihat daftar event miliknya di /events/manage
     */
    public function test_PBI08_TC01_organizer_can_view_event_list()
    {
        $organizer = $this->makeOrganizer();
        $this->makeEvent($organizer, ['title' => 'Pantai Cleanup TC01']);

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('Manage Your Events')
                    ->assertSee('Pantai Cleanup TC01');
        });
    }

    // ─── TC02: View Event Detail ──────────────────────────────────────────────

    /**
     * PBI08-TC02-ViewEventDetail
     * Organizer dapat membuka detail event miliknya
     */
    public function test_PBI08_TC02_organizer_can_view_event_detail()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Detail Event TC02']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/events/' . $event->id)
                    ->assertSee($event->title)
                    ->assertSee($event->location)
                    ->assertSee($event->description);
        });
    }

    // ─── TC03: Create Event ───────────────────────────────────────────────────

    /**
     * PBI08-TC03-CreateEvent
     * Organizer dapat membuat event dengan data valid dan event tersimpan di daftar
     */
    public function test_PBI08_TC03_organizer_can_create_event()
    {
        $organizer = $this->makeOrganizer();
        $title     = 'Create Event TC03 ' . uniqid();

        $this->browse(function (Browser $browser) use ($organizer, $title) {
            $browser->loginAs($organizer)
                    ->visit('/events/create')
                    ->assertSee('Create New Event');

            $this->fillEventForm($browser, ['title' => $title]);

            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil dibuat!', 10)
                    ->assertSee('Event berhasil dibuat!');

            // Verifikasi tersimpan di DB
            $this->assertDatabaseHas('events', [
                'title'        => $title,
                'organizer_id' => $organizer->id,
                'status'       => 'published',
            ]);
        });
    }

    // ─── TC04: Create Event – Empty Field ─────────────────────────────────────

    /**
     * PBI08-TC04-CreateEventEmptyField
     * Sistem menolak pembuatan event jika field wajib kosong
     */
    public function test_PBI08_TC04_create_event_with_empty_required_fields()
    {
        $organizer = $this->makeOrganizer();

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/events/create');

            // Bypass HTML5 required validation
            $browser->script("document.getElementById('eventForm') && document.getElementById('eventForm').setAttribute('novalidate', true);
                              document.querySelector('form') && document.querySelector('form').setAttribute('novalidate', true);");

            // Kosongkan beberapa field, isi quota agar JS alert tidak muncul
            $browser->clear('title')
                    ->clear('location')
                    ->clear('description')
                    ->type('quota', '10')
                    ->click('button[name="action"][value="publish"]')
                    ->assertPathIs('/events/create');

            // Pastikan event tidak tersimpan
            $this->assertDatabaseMissing('events', ['organizer_id' => $organizer->id]);
        });
    }

    // ─── TC05: Update Event ───────────────────────────────────────────────────

    /**
     * PBI08-TC05-UpdateEvent
     * Organizer dapat mengedit event dengan data valid
     */
    public function test_PBI08_TC05_organizer_can_update_event()
    {
        $organizer   = $this->makeOrganizer();
        $event       = $this->makeEvent($organizer, ['title' => 'Event Sebelum Edit TC05']);
        $updatedTitle = 'Event Setelah Edit TC05';

        $this->browse(function (Browser $browser) use ($organizer, $event, $updatedTitle) {
            $date = now()->addDays(20)->format('Y-m-d');

            $browser->loginAs($organizer)
                    ->visit('/events/' . $event->id . '/edit')
                    ->assertSee('Edit Event');

            // Clear dan isi ulang title
            $browser->clear('title')
                    ->type('title', $updatedTitle)
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '10:00';"
                    ]);

            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil diupdate', 10)
                    ->assertSee('Event berhasil diupdate');

            // Verifikasi perubahan di DB
            $this->assertDatabaseHas('events', [
                'id'    => $event->id,
                'title' => $updatedTitle,
            ]);
        });
    }

    // ─── TC06: Update Event – Invalid Data ───────────────────────────────────

    /**
     * PBI08-TC06-UpdateEventInvalidData
     * Sistem menolak update event jika data tidak valid (title kosong)
     */
    public function test_PBI08_TC06_update_event_with_invalid_data()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event Valid TC06']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/events/' . $event->id . '/edit');

            // Bypass HTML5 validation
            $browser->script("document.querySelector('form') && document.querySelector('form').setAttribute('novalidate', true);");

            // Kosongkan title dan submit
            $browser->clear('title')
                    ->click('button[name="action"][value="publish"]')
                    ->assertPathIs('/events/' . $event->id . '/edit');

            // Verifikasi title tidak berubah
            $this->assertDatabaseHas('events', [
                'id'    => $event->id,
                'title' => 'Event Valid TC06',
            ]);
        });
    }

    // ─── TC07: Delete Event ───────────────────────────────────────────────────

    /**
     * PBI08-TC07-DeleteEvent
     * Organizer dapat menghapus event dan event tidak muncul lagi
     */
    public function test_PBI08_TC07_organizer_can_delete_event()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event To Delete TC07']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/events/' . $event->id)
                    ->assertSee($event->title);

            // Kirim DELETE request via JS (karena tidak ada tombol delete di UI detail)
            $browser->script("
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/events/" . $event->id . "';
                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = '_token';
                csrf.value = document.querySelector('meta[name=\"csrf-token\"]').content;
                const method = document.createElement('input');
                method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
                form.appendChild(csrf); form.appendChild(method);
                document.body.appendChild(form); form.submit();
            ");

            $browser->pause(2000);

            // Verifikasi event terhapus dari DB
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
    }

    // ─── TC08: Invalid Event ID ───────────────────────────────────────────────

    /**
     * PBI08-TC08-InvalidEventId
     * Akses event dengan ID tidak ada menampilkan 404 atau redirect
     */
    public function test_PBI08_TC08_invalid_event_id_returns_404()
    {
        $organizer = $this->makeOrganizer();

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/events/99999/edit')
                    ->assertSee('404');
        });
    }

    // ─── TC09: Non-Organizer Access ───────────────────────────────────────────

    /**
     * PBI08-TC09-NonOrganizerAccess
     * Volunteer yang mencoba mengakses route organizer mendapat 403 atau redirect
     */
    public function test_PBI08_TC09_volunteer_cannot_access_organizer_routes()
    {
        $volunteer = User::create([
            'name'     => 'Volunteer TC09',
            'email'    => 'vol' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer',
        ]);
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer);

        $this->browse(function (Browser $browser) use ($volunteer, $event) {
            // Volunteer mencoba edit event orang lain
            $browser->loginAs($volunteer)
                    ->visit('/events/' . $event->id . '/edit')
                    ->assertSee('403');
        });
    }

    // ─── TC10: Unauthenticated Access ────────────────────────────────────────

    /**
     * PBI08-TC10-UnauthenticatedAccess
     * User belum login akan di-redirect ke halaman login
     */
    public function test_PBI08_TC10_unauthenticated_access_redirects_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout()
                    ->visit('/events/create')
                    ->assertPathIs('/login');
        });
    }

    // ─── TC11: Empty Event List ───────────────────────────────────────────────

    /**
     * PBI08-TC11-EmptyEventList
     * Organizer yang belum memiliki event melihat pesan kosong
     */
    public function test_PBI08_TC11_organizer_sees_empty_event_list()
    {
        $organizer = $this->makeOrganizer();

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('No events found.');
        });
    }

    // ─── TC12: Pagination ─────────────────────────────────────────────────────

    /**
     * PBI08-TC12-PaginationEventList
     * Saat event > 6 (per page), pagination muncul dan dapat diklik
     */
    public function test_PBI08_TC12_pagination_event_list()
    {
        $organizer = $this->makeOrganizer();

        // Buat 8 event (melebihi paginate(6))
        for ($i = 1; $i <= 8; $i++) {
            $this->makeEvent($organizer, ['title' => "Pagination Event $i"]);
        }

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('Manage Your Events');

            // Klik halaman 2 via JS
            $browser->script("
                const link = document.querySelector('a[href*=\"page=2\"]');
                if (link) link.click();
            ");

            $browser->pause(1000)
                    ->assertQueryStringHas('page', '2');
        });
    }

    // ─── TC13: Delete Event with Participants ─────────────────────────────────

    /**
     * PBI08-TC13-DeleteEventWithParticipants
     * Event dengan volunteer terdaftar dapat dihapus dan data terkait ditangani
     */
    public function test_PBI08_TC13_delete_event_with_participants()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event With Participants TC13']);

        $volunteer = User::create([
            'name'     => 'Volunteer',
            'email'    => 'vol' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer',
        ]);
        EventRegistration::create([
            'user_id'  => $volunteer->id,
            'event_id' => $event->id,
            'status'   => 'registered',
        ]);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/events/' . $event->id);

            // Hapus via JS form submission
            $browser->script("
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/events/" . $event->id . "';
                const csrf = document.createElement('input');
                csrf.type = 'hidden'; csrf.name = '_token';
                csrf.value = document.querySelector('meta[name=\"csrf-token\"]').content;
                const method = document.createElement('input');
                method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
                form.appendChild(csrf); form.appendChild(method);
                document.body.appendChild(form); form.submit();
            ");

            $browser->pause(2000);

            // Event harus terhapus dari DB
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
    }

    // ─── TC14: Create Event with Image ───────────────────────────────────────

    /**
     * PBI08-TC14-CreateEventWithImage
     * Organizer dapat mengupload gambar saat membuat event
     */
    public function test_PBI08_TC14_create_event_with_image()
    {
        Storage::fake('public');

        $organizer  = $this->makeOrganizer();
        $title      = 'Event With Image TC14 ' . uniqid();

        // Buat file gambar sementara yang valid
        $tempImage = tempnam(sys_get_temp_dir(), 'dusk_img_') . '.jpg';
        file_put_contents($tempImage, base64_decode(
            '/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAMCAgMCAgMDAwMEAwMEBQgFBQQEBQoH' .
            'BwYIDAoMCwsKCwsNCxAQDQ4RDgsLEBYQERMUFRUVDA8XGBYUGBIUFRT/wAARC' .
            'AACAAIDASIA//EABQAAQAAAAAAAAAAAAAAAAAAAAj/xAAUEAEAAAAAAAAAAAAAAAAA' .
            'AAAA/8QAFAEBAAAAAAAAAAAAAAAAAAAAAP/EABQRAQAAAAAAAAAAAAAAAAAAAAD/' .
            '2gAMAwEAAhEDEQA/ADBHP//Z'
        ));

        $this->browse(function (Browser $browser) use ($organizer, $title, $tempImage) {
            $browser->loginAs($organizer)
                    ->visit('/events/create');

            $this->fillEventForm($browser, ['title' => $title]);

            // Upload gambar
            $browser->attach('image', $tempImage);

            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil dibuat!', 10)
                    ->assertSee('Event berhasil dibuat!');

            $this->assertDatabaseHas('events', [
                'title'        => $title,
                'organizer_id' => $organizer->id,
            ]);
        });

        @unlink($tempImage);
    }

    // ─── TC15: Search Event Management ───────────────────────────────────────

    /**
     * PBI08-TC15-SearchEventManagement
     * Organizer dapat mencari event berdasarkan nama di halaman manage
     */
    public function test_PBI08_TC15_organizer_can_search_events()
    {
        $organizer = $this->makeOrganizer();
        $this->makeEvent($organizer, ['title' => 'Beach Cleanup Alpha TC15']);
        $this->makeEvent($organizer, ['title' => 'River Cleanup Beta TC15']);

        $this->browse(function (Browser $browser) use ($organizer) {
            // Tambahkan search via query string langsung (karena manage view tidak punya form search bawaan)
            $browser->loginAs($organizer)
                    ->visit('/events/manage?search=Beach')
                    ->assertSee('Beach Cleanup Alpha TC15')
                    ->assertDontSee('River Cleanup Beta TC15');
        });
    }
}
