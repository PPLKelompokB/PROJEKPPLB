<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

/**
 * RegisterTest - Laravel Dusk Browser Test
 *
 * Pengujian otomatis untuk fitur registrasi pengguna (OceanCare).
 *
 * Struktur Form Register:
 * - Role    : radio button (name="role", value="organizer"|"volunteer"), class="peer sr-only"
 * - Name    : input[name="name"]
 * - Email   : input[name="email"]
 * - Password: input[name="password"]
 * - Confirm : input[name="password_confirmation"]
 * - Submit  : button (teks "Register", tidak ada type="submit")
 *
 * Validasi (RegisterController):
 * - name     : required
 * - email    : required|email|unique:users
 * - password : required|confirmed|min:6
 * - role     : required|in:admin,organizer,volunteer
 *
 * Redirect pasca register:
 * - volunteer → /volunteer/dashboard  (menampilkan "Welcome back, {nama_depan}!")
 * - organizer → /organizer/dashboard (menampilkan "Organizer Dashboard")
 *
 * Catatan implementasi:
 * - Radio button role menggunakan class "peer sr-only" → harus diklik via JavaScript.
 * - Button submit tidak memiliki type="submit" → gunakan ->script() atau ->click('form button').
 * - HTML5 validation (required, type="email") mencegah submit browser langsung
 *   pada TC-REG-05, TC-REG-06, TC-REG-07 → bypass dengan form.submit() via script.
 * - Link "Sign in" berada di dalam overlay fixed z-50 → gunakan CSS selector, bukan clickLink().
 */
class RegisterTest extends DuskTestCase
{
    use DatabaseMigrations;

    // =========================================================================
    // TC-REG-01 : Registrasi sukses sebagai Volunteer (Participant)
    // =========================================================================

    /**
     * @test
     * TC-REG-01
     * Deskripsi  : Pengguna dapat mendaftar sebagai Volunteer dengan data valid.
     * Precondition: Email belum terdaftar di database.
     * Input      : role=volunteer, name="Tae San Volunteer", email, password valid.
     * Expected   : Redirect ke /volunteer/dashboard, muncul teks "Welcome back, Tae".
     */
    public function test_volunteer_can_register_successfully()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            // Pilih role Participant (volunteer) via JavaScript karena radio button hidden (peer sr-only)
            $browser->script("document.querySelector('input[name=\"role\"][value=\"volunteer\"]').click()");
            
            $browser->type('name', 'Tae San Volunteer')
                    ->type('email', 'taesan.vol@mail.com')
                    ->type('password', 'taesan123')
                    ->type('password_confirmation', 'taesan123')
                    // Klik button via CSS selector (button tidak memiliki type="submit")
                    ->click('form button')
                    ->pause(500)
                    // Verifikasi redirect ke halaman volunteer dashboard
                    ->assertPathIs('/volunteer/dashboard')
                    // Verifikasi sambutan nama depan pengguna (explode(' ', name)[0])
                    ->assertSee('Welcome back, Tae');
        });
    }

    // =========================================================================
    // TC-REG-02 : Registrasi sukses sebagai Organizer
    // =========================================================================

    /**
     * @test
     * TC-REG-02
     * Deskripsi  : Pengguna dapat mendaftar sebagai Organizer dengan data valid.
     * Precondition: Email belum terdaftar di database.
     * Input      : role=organizer, name="Woon Hak Organizer", email, password valid.
     * Expected   : Redirect ke /organizer/dashboard, muncul teks "Organizer Dashboard".
     */
    public function test_organizer_can_register_successfully()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            // Pilih role Organizer via JavaScript karena radio button hidden (peer sr-only)
            $browser->script("document.querySelector('input[name=\"role\"][value=\"organizer\"]').click()");
            
            $browser->type('name', 'Woon Hak Organizer')
                    ->type('email', 'woonhak.org@mail.com')
                    ->type('password', 'woonhak123')
                    ->type('password_confirmation', 'woonhak123')
                    ->click('form button')
                    ->pause(500)
                    // Verifikasi redirect ke halaman organizer dashboard
                    ->assertPathIs('/organizer/dashboard')
                    // Verifikasi teks heading halaman organizer dashboard
                    ->assertSee('Organizer Dashboard');
        });
    }

    // =========================================================================
    // TC-REG-03 : Gagal mendaftar — Email sudah terdaftar (duplikat)
    // =========================================================================

    /**
     * @test
     * TC-REG-03
     * Deskripsi  : Registrasi gagal jika email sudah ada di database.
     * Precondition: User dengan email 'duplicate@mail.com' sudah ada.
     * Input      : email=duplicate@mail.com (yang sudah terdaftar).
     * Expected   : Tetap di /register, muncul pesan error tentang 'email'.
     */
    public function test_cannot_register_with_duplicate_email()
    {
        // Precondition: buat user dengan email yang sama
        User::create([
            'name'     => 'Existing User',
            'email'    => 'duplicate@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            $browser->script("document.querySelector('input[name=\"role\"][value=\"volunteer\"]').click()");
            
            $browser->type('name', 'New Name')
                    ->type('email', 'duplicate@mail.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->click('form button')
                    ->pause(500)
                    // Harus tetap di halaman register
                    ->assertPathIs('/register')
                    // Pesan error validasi email harus muncul
                    ->assertSee('email');
        });
    }

    // =========================================================================
    // TC-REG-04 : Gagal mendaftar — Password konfirmasi tidak cocok
    // =========================================================================

    /**
     * @test
     * TC-REG-04
     * Deskripsi  : Registrasi gagal jika password_confirmation tidak sesuai password.
     * Input      : password="password123", password_confirmation="mismatched321"
     * Expected   : Tetap di /register, muncul pesan error tentang 'password'.
     */
    public function test_cannot_register_with_mismatched_passwords()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            $browser->script("document.querySelector('input[name=\"role\"][value=\"volunteer\"]').click()");
            
            $browser->type('name', 'Test User')
                    ->type('email', 'test.user@mail.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'mismatched321')
                    ->click('form button')
                    ->pause(500)
                    // Harus tetap di halaman register
                    ->assertPathIs('/register')
                    // Pesan error validasi password harus muncul
                    ->assertSee('password');
        });
    }

    // =========================================================================
    // TC-REG-05 : Gagal mendaftar — Semua field kosong
    // =========================================================================

    /**
     * @test
     * TC-REG-05
     * Deskripsi  : Registrasi gagal jika semua field dibiarkan kosong.
     * Input      : Semua field kosong, form di-submit langsung.
     * Expected   : Tetap di /register, muncul pesan error validasi server.
     *
     * Catatan: form.submit() digunakan via JavaScript untuk mem-bypass HTML5
     * native required validation, agar validasi server-side (Laravel) yang diuji.
     */
    public function test_cannot_register_with_empty_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            // Submit form langsung via JS → bypass HTML5 required validation
            // sehingga server-side Laravel validation yang menangani error
            $browser->script("document.querySelector('form').submit()");
            
            $browser->pause(700)
                    // Harus tetap di halaman register
                    ->assertPathIs('/register')
                    // Pesan error validasi dari server harus muncul
                    ->assertSee('required');
        });
    }

    // =========================================================================
    // TC-REG-06 : Gagal mendaftar — Password terlalu pendek (< 6 karakter)
    // =========================================================================

    /**
     * @test
     * TC-REG-06
     * Deskripsi  : Registrasi gagal jika password kurang dari 6 karakter.
     * Validasi   : password min:6 (RegisterController).
     * Input      : password="123", password_confirmation="123"
     * Expected   : Tetap di /register, muncul pesan error tentang 'password'.
     *
     * Catatan: form.submit() digunakan via JS untuk bypass HTML5 validation
     * agar validasi min:6 dari server yang teruji.
     */
    public function test_cannot_register_with_short_password()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            $browser->script("document.querySelector('input[name=\"role\"][value=\"volunteer\"]').click()");
            
            $browser->type('name', 'Short Pass User')
                    ->type('email', 'shortpass@mail.com')
                    ->type('password', '123')
                    ->type('password_confirmation', '123');
            
            // Submit via JS untuk bypass HTML5 minlength validation
            $browser->script("document.querySelector('form').submit()");
            
            $browser->pause(700)
                    // Harus tetap di halaman register
                    ->assertPathIs('/register')
                    // Pesan error minimum karakter password harus muncul
                    ->assertSee('password');
        });
    }

    // =========================================================================
    // TC-REG-07 : Gagal mendaftar — Format email tidak valid
    // =========================================================================

    /**
     * @test
     * TC-REG-07
     * Deskripsi  : Registrasi gagal jika format email tidak valid.
     * Validasi   : email|email (RegisterController).
     * Input      : email="bukan-format-email"
     * Expected   : Tetap di /register, muncul pesan error tentang 'email'.
     *
     * Catatan: input type="email" memblok nilai non-email di browser.
     * Nilai dimasukkan via JavaScript setAttribute() untuk bypass validasi browser,
     * lalu form di-submit langsung dengan form.submit() agar server yang memvalidasi.
     */
    public function test_cannot_register_with_invalid_email_format()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->pause(400);
            
            $browser->script("document.querySelector('input[name=\"role\"][value=\"volunteer\"]').click()");
            
            $browser->type('name', 'Invalid Email User')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123');
            
            // Set email tidak valid via JS (bypass browser type="email" validation)
            $browser->script("
                var emailInput = document.querySelector('input[name=\"email\"]');
                emailInput.removeAttribute('type');
                emailInput.value = 'bukan-format-email';
            ");
            
            // Submit langsung via JS agar server-side validation yang diuji
            $browser->script("document.querySelector('form').submit()");
            
            $browser->pause(700)
                    // Harus tetap di halaman register
                    ->assertPathIs('/register')
                    // Pesan error format email dari server harus muncul
                    ->assertSee('email');
        });
    }

    // =========================================================================
    // TC-REG-08 : Halaman register dapat diakses dan menampilkan elemen form
    // =========================================================================

    /**
     * @test
     * TC-REG-08
     * Deskripsi  : Halaman /register dapat diakses dan menampilkan form registrasi.
     * Expected   : Halaman memuat judul "Register" dan semua elemen form tersedia.
     */
    public function test_register_page_is_accessible()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    // Tunggu hingga elemen form muncul (overlay sudah di-render)
                    ->waitFor('form', 5)
                    // Pastikan URL benar
                    ->assertPathIs('/register')
                    // Pastikan judul halaman/form muncul
                    ->waitForText('Register', 5)
                    ->assertSee('Register')
                    // Pastikan tombol submit ada
                    ->assertPresent('button')
                    // Pastikan field name ada
                    ->assertPresent('input[name="name"]')
                    // Pastikan field email ada
                    ->assertPresent('input[name="email"]')
                    // Pastikan field password ada
                    ->assertPresent('input[name="password"]')
                    // Pastikan field konfirmasi password ada
                    ->assertPresent('input[name="password_confirmation"]')
                    // Pastikan radio role volunteer ada
                    ->assertPresent('input[name="role"][value="volunteer"]')
                    // Pastikan radio role organizer ada
                    ->assertPresent('input[name="role"][value="organizer"]');
        });
    }

    // =========================================================================
    // TC-REG-09 : Link "Sign in" mengarah ke halaman login
    // =========================================================================

    /**
     * @test
     * TC-REG-09
     * Deskripsi  : Link "Sign in" di halaman register mengarah ke /login.
     * Expected   : Setelah klik "Sign in", berada di /login.
     *
     * Catatan: link berada di dalam fixed overlay z-50, gunakan CSS selector
     * alih-alih clickLink() agar lebih andal.
     */
    public function test_sign_in_link_redirects_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    // Tunggu hingga link Sign in tersedia
                    ->waitFor('a[href*="login"]', 5)
                    // Klik link via CSS selector (lebih andal dari clickLink() untuk elemen di overlay)
                    ->click('a[href*="login"]')
                    ->pause(400)
                    // Harus diarahkan ke halaman login
                    ->assertPathIs('/login')
                    // Halaman login harus memuat teks Login
                    ->assertSee('Login');
        });
    }

    // =========================================================================
    // TC-REG-10 : Pengguna yang sudah login tidak bisa mengakses halaman register
    // =========================================================================

    /**
     * @test
     * TC-REG-10
     * Deskripsi  : Pengguna yang sudah terautentikasi tidak dapat mengakses /register
     *              karena route dilindungi middleware 'guest'.
     * Precondition: User volunteer sudah terdaftar.
     * Expected   : Redirect otomatis ke dashboard sesuai role (tidak bisa ke /register).
     *
     * Catatan: Setelah loginAs(), browser perlu menavigasi ke satu halaman auth
     * terlebih dahulu (warm-up) agar sesi benar-benar aktif sebelum menguji redirect.
     */
    public function test_authenticated_user_cannot_access_register_page()
    {
        // Buat user volunteer terlebih dahulu
        $user = User::create([
            'name'     => 'Already Logged In',
            'email'    => 'already@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                    // Login sebagai volunteer
                    ->loginAs($user)
                    // Warm-up: navigasi ke dashboard dulu untuk memastikan sesi aktif
                    ->visit('/volunteer/dashboard')
                    ->pause(300)
                    ->assertPathIs('/volunteer/dashboard')
                    // Coba akses halaman register
                    ->visit('/register')
                    ->pause(400)
                    // Middleware 'guest' akan redirect ke halaman lain (bukan /register)
                    ->assertPathIsNot('/register');
        });
    }
}
