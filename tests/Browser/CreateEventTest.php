<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-9: Automation Test – Pembuatan Event oleh Organizer
 *
 * TC-PBI9-01 : Organizer dapat membuka halaman Create Event (form kosong).
 * TC-PBI9-02 : Organizer mengisi form valid → Publish → disimpan, pesan sukses, redirect ke detail event.
 * TC-PBI9-03 : Organizer mengisi form valid → Save as Draft → disimpan draft, pesan sukses, redirect ke detail, TIDAK muncul di halaman publik.
 * TC-PBI9-04 : Organizer mengosongkan kolom wajib → Sistem menampilkan pesan validasi error.
 * TC-PBI9-05 : Organizer mengunggah file dengan format tidak didukung (PDF) → Sistem menampilkan pesan validasi error.
 */
class CreateEventTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ─── Helper ───────────────────────────────────────────────────────────────

    /**
     * Buat user Organizer untuk digunakan di setiap test.
     */
    private function makeOrganizer(): User
    {
        return User::create([
            'name'     => 'Test Organizer',
            'email'    => 'organizer_' . uniqid() . '@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'organizer',
        ]);
    }

    private function fillValidForm(Browser $browser, string $title = 'Beach Cleanup Test Event'): void
    {
        $date = now()->addDays(10)->format('Y-m-d');
        
        $browser->type('title', $title)
                ->type('location', 'Pantai Kuta, Bali')
                // Set date and time using JS to bypass browser locale formatting issues
                ->script([
                    "document.querySelector('input[name=date]').value = '{$date}';",
                    "document.querySelector('input[name=time]').value = '08:00';"
                ]);
        
        $browser->select('duration', '3')
                ->type('quota', '50')
                ->type('description', 'Deskripsi lengkap event pembersihan pantai untuk keperluan testing otomatis.');
    }

    // ─── TC-PBI9-01 ───────────────────────────────────────────────────────────

    /**
     * TC-PBI9-01
     * Organizer mengklik tombol "Create Event" / mengakses halaman create.
     * Expected: Sistem berhasil memuat halaman berisi form pembuatan event kosong.
     */
    public function test_TC_PBI9_01_organizer_can_access_create_event_page(): void
    {
        $organizer = $this->makeOrganizer();

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit(route('events.create'))
                    // Pastikan URL benar
                    ->assertPathIs('/events/create')
                    // Pastikan heading halaman ada
                    ->assertSee('Create New Event')
                    // Pastikan kolom-kolom form kosong tersedia
                    ->assertPresent('input[name="title"]')
                    ->assertPresent('input[name="location"]')
                    ->assertPresent('input[name="date"]')
                    ->assertPresent('input[name="time"]')
                    ->assertPresent('select[name="duration"]')
                    ->assertPresent('input[name="quota"]')
                    ->assertPresent('textarea[name="description"]')
                    ->assertPresent('input[name="image"]')
                    // Pastikan kedua tombol aksi tersedia
                    ->assertSeeIn('button[name="action"][value="publish"]', 'Create Event')
                    ->assertSeeIn('button[name="action"][value="draft"]', 'Save as Draft');
        });
    }

    // ─── TC-PBI9-02 ───────────────────────────────────────────────────────────

    /**
     * TC-PBI9-02
     * Organizer mengisi semua kolom wajib dengan data valid → klik "Create Event" (Publish).
     * Expected: Data tersimpan dengan status "published", pesan sukses "Event berhasil dibuat!",
     *           diarahkan ke halaman detail event, dan event muncul di halaman publik.
     */
    public function test_TC_PBI9_02_organizer_can_publish_event_with_valid_data(): void
    {
        $organizer = $this->makeOrganizer();
        $eventTitle = 'Pantai Cleanup Published ' . uniqid();

        $this->browse(function (Browser $browser) use ($organizer, $eventTitle) {
            $browser->loginAs($organizer)
                    ->visit(route('events.create'))
                    ->assertPathIs('/events/create');

            $this->fillValidForm($browser, $eventTitle);

            $browser->click('button[name="action"][value="publish"]')
                    // Tunggu pesan sukses muncul yang menandakan redirect berhasil
                    ->waitForText('Event berhasil dibuat!', 10)
                    ->assertPathBeginsWith('/events/')
                    // Pesan sukses harus terlihat
                    ->assertSee('Event berhasil dibuat!')
                    // Detail event harus tampil
                    ->assertSee($eventTitle);

            // Verifikasi event tersimpan di DB dengan status published
            $this->assertDatabaseHas('events', [
                'title'  => $eventTitle,
                'status' => 'published',
            ]);

            // Verifikasi event TERLIHAT di halaman publik (/events)
            $event = Event::where('title', $eventTitle)->first();
            $browser->visit('/events')
                    ->assertSee($eventTitle);
        });
    }

    // ─── TC-PBI9-03 ───────────────────────────────────────────────────────────

    /**
     * TC-PBI9-03
     * Organizer mengisi semua kolom wajib dengan data valid → klik "Save as Draft".
     * Expected: Data tersimpan dengan status "draft", pesan sukses "Event berhasil disimpan sebagai draft!",
     *           diarahkan ke halaman detail event, dan event TIDAK TERLIHAT di halaman publik untuk volunteer.
     */
    public function test_TC_PBI9_03_organizer_can_save_event_as_draft(): void
    {
        $organizer = $this->makeOrganizer();
        $eventTitle = 'Draft Event Test ' . uniqid();

        $volunteer = User::create([
            'name'     => 'Test Volunteer',
            'email'    => 'volunteer_' . uniqid() . '@test.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($organizer, $volunteer, $eventTitle) {
            // Organizer membuat event sebagai draft
            $browser->loginAs($organizer)
                    ->visit(route('events.create'))
                    ->assertPathIs('/events/create');

            $this->fillValidForm($browser, $eventTitle);

            $browser->click('button[name="action"][value="draft"]')
                    // Tunggu pesan sukses muncul
                    ->waitForText('Event berhasil disimpan sebagai draft!', 10)
                    ->assertPathBeginsWith('/events/')
                    // Pesan sukses draft
                    ->assertSee('Event berhasil disimpan sebagai draft!')
                    // Detail event tetap tampil (bisa dilihat organizer)
                    ->assertSee($eventTitle);

            // Verifikasi di DB: status harus "draft"
            $this->assertDatabaseHas('events', [
                'title'  => $eventTitle,
                'status' => 'draft',
            ]);

            // Volunteer tidak boleh melihat event draft di halaman publik
            $browser->loginAs($volunteer)
                    ->visit('/events')
                    ->assertDontSee($eventTitle);
        });
    }

    // ─── TC-PBI9-04 ───────────────────────────────────────────────────────────

    /**
     * TC-PBI9-04
     * Organizer mengosongkan kolom wajib (Title) → klik "Create Event".
     * Expected: Sistem MENOLAK menyimpan dan menampilkan pesan validasi
     *           (misal: "The title field is required.").
     */
    public function test_TC_PBI9_04_system_shows_validation_error_when_required_field_is_empty(): void
    {
        $organizer = $this->makeOrganizer();

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit(route('events.create'))
                    ->assertPathIs('/events/create');

            $date = now()->addDays(5)->format('Y-m-d');
            
            // Isi semua kolom KECUALI title (dikosongkan)
            $browser->type('location', 'Pantai Losari, Makassar')
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '09:00';"
                    ]);
                    
            $browser->select('duration', '2')
                    ->type('quota', '30')
                    ->type('description', 'Deskripsi tanpa judul untuk uji validasi.');

            // Pastikan field title benar-benar kosong
            $browser->clear('title');

            // Bypass HTML5 native validation (required attribute)
            // agar validasi server-side Laravel yang diuji
            $browser->script('document.getElementById("eventForm").setAttribute("novalidate", "");');

            // Klik tombol publish
            $browser->click('button[name="action"][value="publish"]');

            // Server-side Laravel validation harus menolak dan kembali ke form
            $browser->assertPathIs('/events/create')
                    // Pesan error validasi harus terlihat
                    ->assertSee('required');

            // Verifikasi TIDAK ada record baru di DB
            $this->assertDatabaseMissing('events', [
                'location' => 'Pantai Losari, Makassar',
                'status'   => 'published',
            ]);
        });
    }

    /**
     * TC-PBI9-04b
     * Variasi lain: Organizer mengosongkan kolom Description → klik "Save as Draft".
     * Expected: Sistem menampilkan pesan validasi.
     */
    public function test_TC_PBI9_04b_system_shows_validation_error_when_description_is_empty(): void
    {
        $organizer = $this->makeOrganizer();

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit(route('events.create'));

            $date = now()->addDays(7)->format('Y-m-d');
            
            // Isi semua KECUALI description
            $browser->type('title', 'Event Tanpa Deskripsi')
                    ->type('location', 'Pantai Anyer')
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '10:00';"
                    ]);
                    
            $browser->select('duration', '4')
                    ->type('quota', '20');

            // Kosongkan description
            $browser->clear('description');

            // Bypass HTML5 native required validation
            $browser->script('document.getElementById("eventForm").setAttribute("novalidate", "");');

            $browser->click('button[name="action"][value="draft"]');

            // Server-side Laravel validation harus menolak dan kembali ke form
            $browser->assertPathIs('/events/create')
                    ->assertSee('required');

            $this->assertDatabaseMissing('events', [
                'title' => 'Event Tanpa Deskripsi',
            ]);
        });
    }

    // ─── TC-PBI9-05 ───────────────────────────────────────────────────────────

    /**
     * TC-PBI9-05
     * Organizer mengunggah file dengan format tidak didukung (.pdf) sebagai gambar poster.
     * Expected: Sistem menolak dan menampilkan pesan validasi
     *           (misal: "The image must be a file of type: jpeg, png, jpg").
     */
    public function test_TC_PBI9_05_system_rejects_invalid_image_format(): void
    {
        Storage::fake('public');

        $organizer = $this->makeOrganizer();

        // Buat file PDF palsu di direktori sementara yang bisa diakses browser
        $fakePdfPath = sys_get_temp_dir() . '/fake_test_document.pdf';
        file_put_contents($fakePdfPath, '%PDF-1.4 fake pdf content for testing');

        $this->browse(function (Browser $browser) use ($organizer, $fakePdfPath) {
            $browser->loginAs($organizer)
                    ->visit(route('events.create'))
                    ->assertPathIs('/events/create');

            // Isi semua kolom wajib dengan data valid
            $this->fillValidForm($browser, 'Event dengan File Tidak Valid');

            // Lampirkan file PDF ke input image
            $browser->attach('image', $fakePdfPath);

            // Klik tombol publish
            $browser->click('button[name="action"][value="publish"]');

            // Sistem harus menolak dan menampilkan error validasi file
            $browser->assertPathIs('/events/create')
                    ->assertSee('image'); // mencakup "The image must be a file of type: jpeg, png, jpg"

            // Verifikasi TIDAK ada event tersimpan di DB
            $this->assertDatabaseMissing('events', [
                'title' => 'Event dengan File Tidak Valid',
            ]);
        });

        // Hapus file PDF sementara
        if (file_exists($fakePdfPath)) {
            unlink($fakePdfPath);
        }
    }
}
