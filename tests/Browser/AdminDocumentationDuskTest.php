<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Documentation;
use App\Models\EventRegistration;
use App\Models\Attendance;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminDocumentationDuskTest extends DuskTestCase
{
    /**
     * Pastikan tabel database sudah ada sebelum test dijalankan.
     * Menggunakan 'migrate' (bukan 'migrate:fresh') agar data sebelumnya
     * TIDAK dihapus — data tetap tersimpan setelah testing selesai.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    // =========================================================
    // HELPER METHODS
    // =========================================================

    private function makeAdmin()
    {
        return User::create([
            'name'     => 'Admin',
            'email'    => 'admin_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'admin'
        ]);
    }

    private function makeOrganizer()
    {
        return User::create([
            'name'     => 'Org',
            'email'    => 'org_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer'
        ]);
    }

    private function makeVolunteer()
    {
        return User::create([
            'name'     => 'Vol',
            'email'    => 'vol_dusk_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'volunteer',
            'points'   => 0
        ]);
    }

    private function makeEvent($organizer, array $overrides = [])
    {
        return Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'title'        => 'Bersih Pantai',
            'description'  => 'Test description',
            'location'     => 'Pantai Kuta',
            'event_date'   => now()->subDays(3)->format('Y-m-d H:i:s'),
            'duration'     => 2,
            'quota'        => 50,
            'status'       => 'published',
        ], $overrides));
    }

    private function makeDocumentation($event, $status = 'pending')
    {
        return Documentation::create([
            'event_id'     => $event->id,
            'organizer_id' => $event->organizer_id,
            'file_path'    => 'doc.jpg',
            'status'       => $status,
            'note'         => 'Laporan akhir bersih pantai'
        ]);
    }

    /**
     * Submit form via JavaScript agar CSRF tetap valid.
     * Mengambil CSRF token via JS karena meta tag ada di <head>, bukan di <body>.
     */
    private function submitFormViaJS(Browser $browser, string $actionUrl, array $fields): Browser
    {
        // Ambil CSRF token langsung dari DOM via JavaScript
        $csrfToken = $browser->driver->executeScript(
            'return document.querySelector(\'meta[name="csrf-token"]\')?.getAttribute(\'content\') || \'\';'
        );

        $allFields  = array_merge(['_token' => $csrfToken], $fields);
        $fieldsJson = json_encode($allFields);

        $browser->script([
            "(function() {
                var data = {$fieldsJson};
                var f = document.createElement('form');
                f.method = 'POST';
                f.action = '{$actionUrl}';
                Object.keys(data).forEach(function(key) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = data[key];
                    f.appendChild(input);
                });
                document.body.appendChild(f);
                f.submit();
            })();"
        ]);

        return $browser;
    }

    // =========================================================
    // TC01 – TC02: LIHAT DAFTAR & DETAIL DOKUMENTASI
    // =========================================================

    /**
     * PBI14-TC01: Admin melihat daftar event yang memiliki dokumentasi
     */
    public function test_PBI14_TC01_ViewDocumentationList()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['title' => 'Event Pantai Indah']);
        $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin) {
            // Step: Admin login -> buka /admin/documentation
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation')
                    ->pause(1500)
                    // Expected: Halaman menampilkan daftar event beserta dokumentasi
                    ->assertSee('Event Documentation')
                    ->assertSee('Event Pantai Indah');
        });
    }

    /**
     * PBI14-TC02: Admin melihat detail dokumentasi suatu event
     */
    public function test_PBI14_TC02_ViewDocumentationDetail()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['title' => 'Detail Event']);
        $this->makeDocumentation($event);

        $this->browse(function (Browser $browser) use ($admin, $event) {
            // Step: Admin login -> buka /admin/documentation/{eventId}
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    // Expected: Halaman menampilkan detail event + file dokumentasi
                    ->assertSee('Event Documentation');
        });
    }

    // =========================================================
    // TC03 – TC04: APPROVE & REJECT DOKUMENTASI
    // =========================================================

    /**
     * PBI14-TC03: Admin menyetujui dokumentasi -> badge berubah "Approved"
     */
    public function test_PBI14_TC03_ApproveDocumentation()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $vol   = $this->makeVolunteer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending');

        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'registered']);
        Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> buka detail -> klik Approve
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->assertSee('Pending')
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    // Expected: Modal konfirmasi muncul
                    ->assertSee('Are you sure you want to approve this documentation?')
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    // Expected: Badge berubah menjadi "Approved"
                    ->assertSee('Approved');
        });
    }

    /**
     * PBI14-TC04: Admin menolak dokumentasi -> badge berubah "Rejected"
     */
    public function test_PBI14_TC04_RejectDocumentation()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> buka detail -> klik Reject
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->assertSee('Pending')
                    ->click("button[onclick*=\"openModal('rejected', {$doc->id})\"]")
                    ->pause(1000)
                    // Expected: Modal konfirmasi muncul
                    ->assertSee('Are you sure you want to reject this documentation?')
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    // Expected: Badge berubah menjadi "Rejected"
                    ->assertSee('Rejected');
        });
    }

    // =========================================================
    // TC05 – TC06: VALIDASI INPUT (TAMPIL ERROR DI BROWSER)
    // =========================================================

    /**
     * PBI14-TC05: Status tidak valid (cancel) -> browser tampilkan respons 422
     */
    public function test_PBI14_TC05_InvalidStatusInput()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> buka halaman detail
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500);

            // Simulasi fetch JS dengan status tidak valid ('cancel') 
            // Memanggil fungsi submitSingle() bawaan show.blade.php
            $browser->script("submitSingle({$doc->id}, 'cancel');");

            $browser->pause(1500)
                    // Expected: Validasi gagal (422) dan UI menampilkan alert error message
                    ->assertDialogOpened('The selected status is invalid.');
            
            $browser->acceptDialog();
        });
    }

    /**
     * PBI14-TC06: Field status kosong -> browser tampilkan respons 422
     */
    public function test_PBI14_TC06_EmptyStatusInput()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> buka halaman detail
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500);

            // Simulasi fetch JS tanpa status ('')
            $browser->script("submitSingle({$doc->id}, '');");

            $browser->pause(1500)
                    // Expected: Validasi gagal (422) dan UI menampilkan alert error message
                    ->assertDialogOpened('The status field is required.');
            
            $browser->acceptDialog();
        });
    }

    // =========================================================
    // TC07 – TC09: AKSES KONTROL (TAMPIL ERROR / REDIRECT)
    // =========================================================

    /**
     * PBI14-TC07: Documentation ID tidak ada -> browser tampilkan 404
     */
    public function test_PBI14_TC07_InvalidDocumentationId()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event) {
            // Step: Admin login -> inject form ke documentation_id=999 yang tidak ada
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500);

            $invalidUrl = route('documentation.verify', 999);
            $this->submitFormViaJS($browser, $invalidUrl, ['status' => 'approved']);

            $browser->pause(1500)
                    // Expected: Browser menampilkan halaman 404 Not Found
                    ->assertSee('404');
        });
    }

    /**
     * PBI14-TC08: Organizer (bukan admin) mencoba verify -> browser tampilkan 403
     */
    public function test_PBI14_TC08_NonAdminAccess()
    {
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($org, $event, $doc) {
            // Step: Login sebagai ORGANIZER -> coba akses /admin/documentation
            $browser->driver->manage()->deleteAllCookies();
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $org->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation')
                    ->pause(1500)
                    // Expected: Browser menampilkan halaman 403 Forbidden
                    ->assertSee('403');
        });
    }

    /**
     * PBI14-TC09: User belum login -> browser redirect ke halaman login
     */
    public function test_PBI14_TC09_UnauthenticatedAccess()
    {
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($event) {
            $browser->driver->manage()->deleteAllCookies();
            
            // Step: Tanpa login -> coba buka halaman admin documentation
            $browser->visit('/admin/documentation')
                    ->pause(1500)
                    // Expected: Browser redirect ke halaman login
                    ->assertPathIs('/login')
                    ->assertSee('Login');
        });
    }

    // =========================================================
    // TC10 – TC15: FITUR POIN, NOTIFIKASI & PAGINATION
    // =========================================================

    /**
     * PBI14-TC10: Approve event dengan 3 volunteer -> poin diberikan ke semua
     */
    public function test_PBI14_TC10_PointCalculationApproval()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['duration' => 2]);
        $doc   = $this->makeDocumentation($event, 'pending');

        for ($i = 0; $i < 3; $i++) {
            $vol = $this->makeVolunteer();
            EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'registered']);
            Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'present']);
        }

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> approve dokumentasi
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    // Expected: Status berubah Approved, poin diberikan ke volunteer
                    ->waitForText('Approved', 10);
        });

        // Expected: Cek record poin di DB (duration=2 -> 20 poin per volunteer)
        $this->assertDatabaseCount('points', 3);
    }

    /**
     * PBI14-TC11: Approve dua kali -> tidak ada duplikat poin di DB
     */
    public function test_PBI14_TC11_NoDuplicatePoints()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['duration' => 2]);
        $doc   = $this->makeDocumentation($event, 'pending');

        $vol = $this->makeVolunteer();
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'registered']);
        Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin approve pertama kali
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    ->waitForText('Approved', 10);

            // Kembalikan status ke pending untuk simulasi approve ulang
            \App\Models\Documentation::find($doc->id)->update(['status' => 'pending']);

            // Step: Admin approve kedua kali
            $browser->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    ->waitForText('Approved', 10);
        });

        // Expected: Hanya 1 record poin per volunteer (tidak duplikat)
        $this->assertDatabaseCount('points', 1);
    }

    /**
     * PBI14-TC12: Approve dokumentasi -> notifikasi "Documentation Approved" tersimpan
     */
    public function test_PBI14_TC12_NotificationApproved()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['title' => 'Bersih Pantai']);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> approve dokumentasi event "Bersih Pantai"
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    ->assertSee('Approved');
        });

        // Expected: Notifikasi tersimpan dengan title mengandung "Documentation Approved"
        $this->assertDatabaseHas('notifications', [
            'user_id' => $org->id,
            'type'    => 'success',
            'is_read' => false,
        ]);
    }

    /**
     * PBI14-TC13: Reject dokumentasi -> notifikasi "Documentation Rejected" tersimpan
     */
    public function test_PBI14_TC13_NotificationRejected()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org, ['title' => 'Bersih Pantai']);
        $doc   = $this->makeDocumentation($event, 'pending');

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> reject dokumentasi event "Bersih Pantai"
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->click("button[onclick*=\"openModal('rejected', {$doc->id})\"]")
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    ->assertSee('Rejected');
        });

        // Expected: Notifikasi tersimpan dengan type error
        $this->assertDatabaseHas('notifications', [
            'user_id' => $org->id,
            'type'    => 'error',
            'is_read' => false,
        ]);
    }

    /**
     * PBI14-TC14: Approve dokumentasi yang memiliki note -> note terlampir di notifikasi
     */
    public function test_PBI14_TC14_ApproveWithNote()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();
        $event = $this->makeEvent($org);
        $doc   = $this->makeDocumentation($event, 'pending'); // note = 'Laporan akhir bersih pantai'

        $vol = $this->makeVolunteer();
        EventRegistration::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'registered']);
        Attendance::create(['user_id' => $vol->id, 'event_id' => $event->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($admin, $event, $doc) {
            // Step: Admin login -> approve dokumentasi yang memiliki note
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation/' . $event->id)
                    ->pause(1500)
                    ->click("button[onclick*=\"openModal('approved', {$doc->id})\"]")
                    ->pause(1000)
                    ->click('#confirmModalBtn')
                    ->pause(2000)
                    // Expected: Status berubah Approved
                    ->assertSee('Approved');
        });

        // Expected: Notifikasi mencantumkan note, poin diberikan ke volunteer
        $this->assertDatabaseHas('notifications', ['user_id' => $org->id, 'type' => 'success']);
        $this->assertDatabaseHas('points', ['user_id' => $vol->id, 'points' => 20]);
    }

    /**
     * PBI14-TC15: Lebih dari 10 event dengan dokumentasi -> pagination tampil di browser
     */
    public function test_PBI14_TC15_PaginationDocumentationList()
    {
        $admin = $this->makeAdmin();
        $org   = $this->makeOrganizer();

        // Precondition: buat 13 event dengan dokumentasi agar melewati batas 10 per halaman
        for ($i = 1; $i <= 13; $i++) {
            $event = $this->makeEvent($org, ['title' => "Event Pantai $i"]);
            $this->makeDocumentation($event);
        }

        $this->browse(function (Browser $browser) use ($admin) {
            // Step: Admin login -> buka /admin/documentation
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(2000)
                    ->visit('/admin/documentation')
                    ->pause(1500)
                    // Expected: Tampil pagination, navigasi halaman tersedia
                    ->assertSee('Event Documentation')
                    ->assertPresent('nav[aria-label="pagination"], .pagination, [aria-label="Pagination"], nav');
        });
    }
}

