<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

/**
 * ProfileTest - Laravel Dusk Browser Test
 *
 * Pengujian otomatis untuk fitur manajemen profil pengguna (OceanCare).
 *
 * Skenario Pengujian:
 * - TC-PROF-01: Halaman profil menampilkan detail data pengguna.
 * - TC-PROF-02: Mengedit profil dengan data baru yang valid berhasil.
 * - TC-PROF-03: Mengedit profil dengan unggahan foto profil berhasil.
 * - TC-PROF-04: Gagal mengedit profil jika field wajib kosong.
 * - TC-PROF-05: Gagal mengedit profil jika email duplikat.
 * - TC-PROF-06: Validasi berkas foto profil tidak sesuai format/ukuran (klien & server).
 */
class ProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    // =========================================================================
    // TC-PROF-01 : Halaman profil menampilkan detail data pengguna
    // =========================================================================

    /**
     * @test
     * TC-PROF-01
     * Deskripsi  : Halaman /profile dapat diakses dan menampilkan detail data pengguna dengan benar.
     * Precondition: Pengguna sudah login.
     * Expected   : Menampilkan Nama, Email, Telepon, Lokasi sesuai database.
     */
    public function test_profile_page_displays_user_details()
    {
        $user = User::create([
            'name'     => 'Rheina Volunteer',
            'email'    => 'rheina@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
            'phone'    => '081234567890',
            'location' => 'Surabaya, Indonesia',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile')
                    ->waitForText('Profile', 5)
                    ->assertSee($user->name)
                    ->assertSee($user->email)
                    ->assertSee($user->phone)
                    ->assertSee($user->location);
        });
    }

    // =========================================================================
    // TC-PROF-02 : Mengedit profil dengan data baru yang valid berhasil
    // =========================================================================

    /**
     * @test
     * TC-PROF-02
     * Deskripsi  : Pengguna dapat mengedit nama, email, nomor telepon, dan lokasi dengan data valid.
     * Precondition: Pengguna sudah login.
     * Expected   : Berhasil memperbarui data, redirect ke /profile, muncul pesan sukses.
     */
    public function test_can_edit_profile_with_valid_data()
    {
        $user = User::create([
            'name'     => 'Rheina Volunteer',
            'email'    => 'rheina@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/edit')
                    ->waitFor('form[action*="profile"]', 5)
                    // Isi data baru
                    ->type('name', 'Rheina Updated')
                    ->type('email', 'rheina.updated@mail.com')
                    ->type('phone', '08999999999')
                    ->type('location', 'Jakarta, Indonesia')
                    // Klik tombol submit milik form profil (bukan logout form di navbar)
                    ->click('form[action*="profile"] button[type="submit"]')
                    ->pause(500)
                    // Verifikasi redirect & pesan sukses
                    ->assertPathIs('/profile')
                    ->assertSee('Profil berhasil diperbarui.')
                    // Verifikasi data baru ter-render di halaman profil
                    ->assertSee('Rheina Updated')
                    ->assertSee('rheina.updated@mail.com')
                    ->assertSee('08999999999')
                    ->assertSee('Jakarta, Indonesia');
        });
    }

    // =========================================================================
    // TC-PROF-03 : Mengedit profil dengan unggahan foto profil berhasil
    // =========================================================================

    /**
     * @test
     * TC-PROF-03
     * Deskripsi  : Pengguna dapat mengganti foto profil dengan mengunggah gambar baru yang valid.
     * Precondition: Pengguna sudah login.
     * Expected   : Foto profil berhasil diunggah, redirect ke /profile dengan pesan sukses.
     */
    public function test_can_upload_valid_profile_photo()
    {
        $user = User::create([
            'name'     => 'Rheina Volunteer',
            'email'    => 'rheina@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        // Buat dummy PNG image menggunakan data base64 (1x1 transparent PNG)
        $pngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
        $tempImage = tempnam(sys_get_temp_dir(), 'profile_photo') . '.png';
        file_put_contents($tempImage, base64_decode($pngBase64));

        $this->browse(function (Browser $browser) use ($user, $tempImage) {
            $browser->loginAs($user)
                    ->visit('/profile/edit')
                    ->waitFor('form[action*="profile"]', 5)
                    // Tampilkan file input tersembunyi agar Dusk bisa berinteraksi
                    ->script("document.getElementById('photo_profile').classList.remove('hidden')");

            $browser->attach('photo_profile', $tempImage)
                    ->click('form[action*="profile"] button[type="submit"]')
                    ->pause(500)
                    // Verifikasi sukses
                    ->assertPathIs('/profile')
                    ->assertSee('Profil berhasil diperbarui.');
        });

        // Hapus berkas temporary
        @unlink($tempImage);
    }

    // =========================================================================
    // TC-PROF-04 : Gagal mengedit profil jika field wajib kosong
    // =========================================================================

    /**
     * @test
     * TC-PROF-04
     * Deskripsi  : Menguji kegagalan pembaruan profil saat nama dan email dikosongkan.
     * Precondition: Pengguna sudah login.
     * Expected   : Tetap di halaman edit (atau redirect kembali dengan error), muncul pesan error validasi.
     */
    public function test_cannot_save_profile_with_empty_required_fields()
    {
        $user = User::create([
            'name'     => 'Rheina Volunteer',
            'email'    => 'rheina@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/profile/edit')
                    ->waitFor('form[action*="profile"]', 5)
                    // Kosongkan name dan email via JS/Type
                    ->type('name', '')
                    ->type('email', '')
                    // Submit langsung form profil via JS untuk bypass HTML5 'required' validation
                    ->script("document.querySelector('form[action*=\"profile\"]').submit()");

            $browser->pause(700)
                    // Harus tetap di /profile/edit (redirect back)
                    ->assertPathIs('/profile/edit')
                    // Memverifikasi pesan kesalahan validasi dari controller
                    ->assertSee('Nama lengkap wajib diisi.')
                    ->assertSee('Email wajib diisi.');
        });
    }

    // =========================================================================
    // TC-PROF-05 : Gagal mengedit profil jika email duplikat
    // =========================================================================

    /**
     * @test
     * TC-PROF-05
     * Deskripsi  : Pengguna tidak dapat menggunakan email yang sudah terdaftar pada pengguna lain.
     * Precondition: Pengguna lain terdaftar dengan email 'other@mail.com'.
     * Expected   : Gagal memperbarui, muncul pesan kesalahan keunikan email.
     */
    public function test_cannot_use_already_registered_email()
    {
        $user1 = User::create([
            'name'     => 'Rheina Volunteer',
            'email'    => 'rheina@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        User::create([
            'name'     => 'Other User',
            'email'    => 'other@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        $this->browse(function (Browser $browser) use ($user1) {
            $browser->loginAs($user1)
                    ->visit('/profile/edit')
                    ->waitFor('form[action*="profile"]', 5)
                    ->type('email', 'other@mail.com')
                    ->click('form[action*="profile"] button[type="submit"]')
                    ->pause(500)
                    // Gagal & diredirect kembali ke form edit dengan error
                    ->assertPathIs('/profile/edit')
                    ->assertSee('Email sudah digunakan oleh pengguna lain.');
        });
    }

    // =========================================================================
    // TC-PROF-06 : Validasi berkas foto profil tidak sesuai format/ukuran
    // =========================================================================

    /**
     * @test
     * TC-PROF-06
     * Deskripsi  : Menguji validasi sisi klien (JavaScript) jika format berkas/ukuran berkas tidak sesuai.
     * Precondition: Pengguna sudah login.
     * Expected   : Client-side error message muncul di bawah avatar, input berkas dikosongkan.
     */
    public function test_client_side_validation_for_invalid_profile_photo()
    {
        $user = User::create([
            'name'     => 'Rheina Volunteer',
            'email'    => 'rheina@mail.com',
            'password' => bcrypt('password123'),
            'role'     => 'volunteer',
        ]);

        // Buat dummy text file
        $tempTextFile = tempnam(sys_get_temp_dir(), 'profile_text') . '.txt';
        file_put_contents($tempTextFile, 'not an image file');

        // Buat file yang terlalu besar (> 2MB, misal 2.1 MB)
        $tempLargeFile = tempnam(sys_get_temp_dir(), 'profile_large') . '.jpg';
        $fp = fopen($tempLargeFile, 'w');
        fseek($fp, 2.1 * 1024 * 1024);
        fwrite($fp, 'a');
        fclose($fp);

        $this->browse(function (Browser $browser) use ($user, $tempTextFile, $tempLargeFile) {
            $browser->loginAs($user)
                    ->visit('/profile/edit')
                    ->waitFor('form[action*="profile"]', 5)
                    ->script("document.getElementById('photo_profile').classList.remove('hidden')");

            // 1. Uji validasi format berkas salah (.txt)
            $browser->attach('photo_profile', $tempTextFile)
                    ->pause(300)
                    ->assertSeeIn('#clientErrorMsg', 'Only JPG, JPEG, and PNG formats are allowed.');

            // 2. Uji validasi ukuran berkas terlalu besar (> 2MB)
            $browser->attach('photo_profile', $tempLargeFile)
                    ->pause(300)
                    ->assertSeeIn('#clientErrorMsg', 'Image size cannot exceed 2 MB.');
        });

        // Hapus berkas temporary
        @unlink($tempTextFile);
        @unlink($tempLargeFile);
    }
}
