<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

/**
 * LogoutTest - Laravel Dusk Browser Test
 *
 * Pengujian otomatis untuk fitur logout pengguna (OceanCare).
 *
 * Skenario Pengujian:
 * - TC-OUT-01: Logout sukses sebagai Volunteer.
 * - TC-OUT-02: Logout sukses sebagai Organizer.
 * - TC-OUT-03: Logout sukses sebagai Admin.
 * - TC-OUT-04: Mencegah akses kembali ke dashboard setelah logout.
 * - TC-OUT-05: Tamu (unauthenticated) diarahkan ke halaman login jika memanggil route logout POST.
 */
class LogoutTest extends DuskTestCase
{
    use DatabaseMigrations;

    // =========================================================================
    // TC-OUT-01 : Logout sukses sebagai Volunteer
    // =========================================================================

    /**
     * @test
     * TC-OUT-01
     * Deskripsi  : Pengguna dapat keluar dari sistem dengan menekan tombol Logout sebagai Volunteer.
     * Precondition: Pengguna sudah login sebagai volunteer.
     * Expected   : Redirect ke / (landing page), sesi berakhir (assertGuest).
     */
    public function test_volunteer_can_logout_successfully()
    {
        $user = User::create([
            'name'     => 'Tae San Volunteer',
            'email'    => 'taesan.vol@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/volunteer/dashboard')
                    ->waitFor('form[action*="logout"]', 5)
                    ->pause(300)
                    ->click('form[action*="logout"] button')
                    ->pause(500)
                    ->assertPathIs('/')
                    ->assertGuest();
        });
    }

    // =========================================================================
    // TC-OUT-02 : Logout sukses sebagai Organizer
    // =========================================================================

    /**
     * @test
     * TC-OUT-02
     * Deskripsi  : Pengguna dapat keluar dari sistem dengan menekan tombol Logout sebagai Organizer.
     * Precondition: Pengguna sudah login sebagai organizer.
     * Expected   : Redirect ke / (landing page), sesi berakhir (assertGuest).
     */
    public function test_organizer_can_logout_successfully()
    {
        $user = User::create([
            'name'     => 'Woon Hak Organizer',
            'email'    => 'woonhak.org@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'organizer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/organizer/dashboard')
                    ->waitFor('form[action*="logout"]', 5)
                    ->pause(300)
                    ->click('form[action*="logout"] button')
                    ->pause(500)
                    ->assertPathIs('/')
                    ->assertGuest();
        });
    }

    // =========================================================================
    // TC-OUT-03 : Logout sukses sebagai Admin
    // =========================================================================

    /**
     * @test
     * TC-OUT-03
     * Deskripsi  : Pengguna dapat keluar dari sistem dengan menekan tombol Logout sebagai Admin.
     * Precondition: Pengguna sudah login sebagai admin.
     * Expected   : Redirect ke / (landing page), sesi berakhir (assertGuest).
     */
    public function test_admin_can_logout_successfully()
    {
        $user = User::create([
            'name'     => 'Admin OceanCare',
            'email'    => 'admin@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'admin',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/admin/dashboard')
                    ->waitFor('form[action*="logout"]', 5)
                    ->pause(300)
                    ->click('form[action*="logout"] button')
                    ->pause(500)
                    ->assertPathIs('/')
                    ->assertGuest();
        });
    }

    // =========================================================================
    // TC-OUT-04 : Mencegah akses kembali ke dashboard setelah logout
    // =========================================================================

    /**
     * @test
     * TC-OUT-04
     * Deskripsi  : Pengguna tidak dapat mengakses kembali halaman dashboard setelah melakukan logout.
     * Precondition: Pengguna sudah login dan kemudian logout.
     * Expected   : Diarahkan ke /login saat mencoba mengakses halaman dashboard.
     */
    public function test_cannot_access_dashboard_after_logout()
    {
        $user = User::create([
            'name'     => 'Tae San Volunteer',
            'email'    => 'taesan.vol@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // Login & Logout
            $browser->loginAs($user)
                    ->visit('/volunteer/dashboard')
                    ->waitFor('form[action*="logout"]', 5)
                    ->click('form[action*="logout"] button')
                    ->pause(500)
                    ->assertPathIs('/')
                    ->assertGuest()
                    // Coba akses kembali dashboard
                    ->visit('/volunteer/dashboard')
                    ->pause(500)
                    // Harus diredirect ke halaman login
                    ->assertPathIs('/login');
        });
    }

    // =========================================================================
    // TC-OUT-05 : Tamu (unauthenticated) diarahkan ke halaman login jika memanggil route logout
    // =========================================================================

    /**
     * @test
     * TC-OUT-05
     * Deskripsi  : Pengguna unauthenticated yang menembak route logout (POST) akan diredirect ke login.
     * Expected   : Redirect ke /login.
     */
    public function test_guest_is_redirected_to_login_on_logout_request()
    {
        $this->browse(function (Browser $browser) {
            // Buka halaman login untuk mengambil CSRF token
            $browser->visit('/login')
                    ->waitFor('form', 5);
            
            // Submit form logout menggunakan CSRF token yang valid dari halaman login
            $browser->script("
                var csrfToken = document.querySelector('input[name=\"_token\"]').value;
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/logout';
                var csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            ");
            
            $browser->pause(700)
                    // Karena tidak terautentikasi, middleware auth akan mengarahkan request POST /logout ke login.
                    ->assertPathIs('/login');
        });
    }
}
