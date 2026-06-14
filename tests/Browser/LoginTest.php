<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

/**
 * LoginTest - Laravel Dusk Browser Test
 *
 * Pengujian otomatis untuk fitur login dan logout pengguna (OceanCare).
 *
 * Struktur Form Login:
 * - Email   : input[type="email", name="email"], required
 * - Password: input[type="password", name="password"], required
 * - Submit  : button (teks "Login", tidak ada type="submit")
 *
 * Validasi (LoginController@store):
 * - email    : required|email
 * - password : required
 * - Pesan error: "Email atau password salah" (Auth::attempt gagal)
 *
 * Redirect pasca login:
 * - volunteer → /volunteer/dashboard  (menampilkan "Welcome back, {nama_depan}!")
 * - organizer → /organizer/dashboard (menampilkan "Organizer Dashboard")
 * - admin     → /admin/dashboard     (menampilkan "Admin Dashboard")
 *
 * Logout (LoginController@destroy):
 * - Route: POST /logout (middleware auth)
 * - Tombol: form button teks "Logout" di navbar
 * - Redirect: ke / (landing page)
 *
 * Catatan implementasi:
 * - Button login tidak memiliki type="submit" → gunakan ->click('form button').
 * - Link "Sign up" berada di dalam overlay fixed z-50 → gunakan ->click('a[href*="register"]').
 * - TC-LOG-06 (empty fields) dan TC-LOG-07 (invalid email): bypass HTML5 validation
 *   via form.submit() agar server-side Laravel validation yang diuji.
 * - TC-LOG-10 (auth → login redirect): gunakan warm-up visit ke dashboard terlebih dahulu.
 */
class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    // =========================================================================
    // TC-LOG-01 : Login sukses sebagai Volunteer
    // =========================================================================

    /**
     * @test
     * TC-LOG-01
     * Deskripsi  : Pengguna dapat masuk sebagai Volunteer dengan data valid.
     * Precondition: User volunteer sudah terdaftar di database.
     * Input      : email="taesan.vol@mail.com", password="password123".
     * Expected   : Redirect ke /volunteer/dashboard, muncul teks "Welcome back, Tae".
     */
    public function test_volunteer_can_login_successfully()
    {
        $user = User::create([
            'name'     => 'Tae San Volunteer',
            'email'    => 'taesan.vol@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->waitFor('form', 5)
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->click('form button')
                    ->pause(500)
                    // Verifikasi redirect ke dashboard volunteer
                    ->assertPathIs('/volunteer/dashboard')
                    // Verifikasi sambutan (explode(' ', name)[0] → "Tae")
                    ->assertSee('Welcome back, Tae');
        });
    }

    // =========================================================================
    // TC-LOG-02 : Login sukses sebagai Organizer
    // =========================================================================

    /**
     * @test
     * TC-LOG-02
     * Deskripsi  : Pengguna dapat masuk sebagai Organizer dengan data valid.
     * Precondition: User organizer sudah terdaftar di database.
     * Input      : email="woonhak.org@mail.com", password="password123".
     * Expected   : Redirect ke /organizer/dashboard, muncul teks "Organizer Dashboard".
     */
    public function test_organizer_can_login_successfully()
    {
        $user = User::create([
            'name'     => 'Woon Hak Organizer',
            'email'    => 'woonhak.org@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'organizer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->waitFor('form', 5)
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->click('form button')
                    ->pause(500)
                    // Verifikasi redirect ke dashboard organizer
                    ->assertPathIs('/organizer/dashboard')
                    ->assertSee('Organizer Dashboard');
        });
    }

    // =========================================================================
    // TC-LOG-03 : Login sukses sebagai Admin
    // =========================================================================

    /**
     * @test
     * TC-LOG-03
     * Deskripsi  : Pengguna dapat masuk sebagai Admin dengan data valid.
     * Precondition: User admin sudah terdaftar di database.
     * Input      : email="admin@mail.com", password="password123".
     * Expected   : Redirect ke /admin/dashboard, muncul teks "Admin Dashboard".
     */
    public function test_admin_can_login_successfully()
    {
        $user = User::create([
            'name'     => 'Admin OceanCare',
            'email'    => 'admin@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->waitFor('form', 5)
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->click('form button')
                    ->pause(500)
                    // Verifikasi redirect ke dashboard admin
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('Admin Dashboard');
        });
    }

    // =========================================================================
    // TC-LOG-04 : Gagal login — Password salah
    // =========================================================================

    /**
     * @test
     * TC-LOG-04
     * Deskripsi  : Login gagal jika kata sandi tidak sesuai.
     * Precondition: User terdaftar dengan email 'user@mail.com'.
     * Input      : email="user@mail.com", password="wrongpassword".
     * Expected   : Tetap di /login, muncul pesan "Email atau password salah".
     */
    public function test_cannot_login_with_incorrect_password()
    {
        User::create([
            'name'     => 'Test User',
            'email'    => 'user@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('form', 5)
                    ->type('email', 'user@mail.com')
                    ->type('password', 'wrongpassword')
                    ->click('form button')
                    ->pause(500)
                    // Harus tetap di halaman login
                    ->assertPathIs('/login')
                    // Pesan error dari LoginController harus muncul
                    ->assertSee('Email atau password salah');
        });
    }

    // =========================================================================
    // TC-LOG-05 : Gagal login — Email tidak terdaftar
    // =========================================================================

    /**
     * @test
     * TC-LOG-05
     * Deskripsi  : Login gagal jika email tidak ditemukan di database.
     * Input      : email="unregistered@mail.com", password="password123".
     * Expected   : Tetap di /login, muncul pesan "Email atau password salah".
     */
    public function test_cannot_login_with_unregistered_email()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('form', 5)
                    ->type('email', 'unregistered@mail.com')
                    ->type('password', 'password123')
                    ->click('form button')
                    ->pause(500)
                    ->assertPathIs('/login')
                    ->assertSee('Email atau password salah');
        });
    }

    // =========================================================================
    // TC-LOG-06 : Gagal login — Semua field kosong
    // =========================================================================

    /**
     * @test
     * TC-LOG-06
     * Deskripsi  : Login gagal jika email dan password dikosongkan.
     * Input      : Semua field kosong.
     * Expected   : Tetap di /login, validasi server menolak form.
     *
     * Catatan: form.submit() digunakan via JavaScript untuk bypass HTML5
     * required validation, agar validasi server-side Laravel yang diuji.
     */
    public function test_cannot_login_with_empty_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('form', 5);
            
            // Submit form langsung via JS → bypass HTML5 required validation
            $browser->script("document.querySelector('form').submit()");
            
            $browser->pause(700)
                    ->assertPathIs('/login');
        });
    }

    // =========================================================================
    // TC-LOG-07 : Gagal login — Format email tidak valid
    // =========================================================================

    /**
     * @test
     * TC-LOG-07
     * Deskripsi  : Login gagal jika format email tidak sesuai standar.
     * Input      : email="invalid-email-format", password="password123".
     * Expected   : Tetap di /login.
     *
     * Catatan: input type="email" memblok nilai non-email di browser.
     * Nilai dimasukkan via JavaScript setAttribute() untuk bypass validasi browser.
     */
    public function test_cannot_login_with_invalid_email_format()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->waitFor('form', 5)
                    ->type('password', 'password123');
            
            // Set email tidak valid via JS (bypass browser type="email" validation)
            $browser->script("
                var emailInput = document.querySelector('input[name=\"email\"]');
                emailInput.removeAttribute('type');
                emailInput.value = 'invalid-email-format';
            ");
            
            // Submit via JS agar server-side validation yang menangani
            $browser->script("document.querySelector('form').submit()");
            
            $browser->pause(700)
                    ->assertPathIs('/login');
        });
    }

    // =========================================================================
    // TC-LOG-08 : Halaman login dapat diakses dan menampilkan elemen form
    // =========================================================================

    /**
     * @test
     * TC-LOG-08
     * Deskripsi  : Halaman /login dapat diakses dan menampilkan form login lengkap.
     * Expected   : URL /login, teks "Welcome Back", input email & password, tombol Login.
     */
    public function test_login_page_is_accessible()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    // Tunggu hingga form overlay ter-render
                    ->waitFor('form', 5)
                    // Pastikan URL benar
                    ->assertPathIs('/login')
                    // Pastikan heading halaman muncul
                    ->waitForText('Welcome Back', 5)
                    ->assertSee('Welcome Back')
                    // Pastikan semua elemen form ada
                    ->assertPresent('input[name="email"]')
                    ->assertPresent('input[name="password"]')
                    ->assertPresent('button')
                    // Pastikan link Sign up ada
                    ->assertPresent('a[href*="register"]');
        });
    }

    // =========================================================================
    // TC-LOG-09 : Link "Sign up" mengarah ke halaman register
    // =========================================================================

    /**
     * @test
     * TC-LOG-09
     * Deskripsi  : Klik link "Sign up" mengarahkan pengguna ke halaman pendaftaran.
     * Expected   : Setelah klik, URL berubah ke /register dan muncul teks "Register".
     *
     * Catatan: link berada di dalam overlay fixed z-50, gunakan CSS selector
     * alih-alih clickLink() agar lebih andal.
     */
    public function test_sign_up_link_redirects_to_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    // Tunggu link Sign up muncul
                    ->waitFor('a[href*="register"]', 5)
                    // Klik via CSS selector (lebih andal dari clickLink() untuk elemen di overlay)
                    ->click('a[href*="register"]')
                    ->pause(400)
                    // Harus diarahkan ke halaman register
                    ->assertPathIs('/register')
                    ->assertSee('Register');
        });
    }

    // =========================================================================
    // TC-LOG-10 : Pengguna yang sudah masuk tidak bisa mengakses halaman login
    // =========================================================================

    /**
     * @test
     * TC-LOG-10
     * Deskripsi  : Pengguna terautentikasi dialihkan dari halaman login (guest middleware).
     * Precondition: User volunteer sudah terdaftar.
     * Expected   : Redirect otomatis menjauh dari /login.
     *
     * Catatan: Setelah loginAs(), browser perlu menavigasi ke satu halaman auth
     * terlebih dahulu (warm-up) agar sesi benar-benar aktif sebelum menguji redirect.
     */
    public function test_authenticated_user_cannot_access_login_page()
    {
        $user = User::create([
            'name'     => 'Already Logged In',
            'email'    => 'already@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    // Warm-up: navigasi ke dashboard dulu agar sesi aktif
                    ->visit('/volunteer/dashboard')
                    ->pause(300)
                    ->assertPathIs('/volunteer/dashboard')
                    // Coba akses halaman login
                    ->visit('/login')
                    ->pause(400)
                    // Middleware 'guest' akan redirect ke halaman lain
                    ->assertPathIsNot('/login');
        });
    }

    // =========================================================================
    // TC-LOG-11 : Logout sukses
    // =========================================================================

    /**
     * @test
     * TC-LOG-11
     * Deskripsi  : Pengguna dapat keluar dari sistem dengan menekan tombol Logout.
     * Precondition: Pengguna sudah login sebagai volunteer.
     * Expected   : Redirect ke / (landing page), sesi berakhir (assertGuest).
     */
    public function test_user_can_logout_successfully()
    {
        $user = User::create([
            'name'     => 'Active User',
            'email'    => 'active@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    // Kunjungi dashboard volunteer (tombol Logout ada di navbar)
                    ->visit('/volunteer/dashboard')
                    ->waitFor('form[action*="logout"]', 5)
                    ->pause(300)
                    // Klik tombol Logout di navbar (form POST /logout)
                    ->click('form[action*="logout"] button')
                    ->pause(500)
                    // Verifikasi redirect ke landing page
                    ->assertPathIs('/')
                    // Verifikasi sesi telah berakhir (user is guest)
                    ->assertGuest();
        });
    }
}
