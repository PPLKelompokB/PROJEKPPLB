<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NotificationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * TC-PBI15-03: Organizer melihat badge notifikasi merah.
     * TC-PBI15-04: Organizer mengklik ikon lonceng dan melihat dropdown Unread.
     */
    public function test_organizer_sees_unread_notification_badge_and_dropdown()
    {
        $organizer = User::create([
            'name' => 'Test Org',
            'email' => 'org'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);
        
        // Buat notifikasi unread
        Notification::create([
            'user_id' => $organizer->id,
            'title' => 'Your event documentation has been approved by admin.',
            'message' => 'Good job!',
            'type' => 'success',
            'is_read' => false
        ]);

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/dashboard') // Sesuaikan dengan route dashboard organizer
                    // Memastikan ada indikator angka/badge pada icon lonceng
                    ->pause(1500)
                    ->waitFor('#notifBadge')
                    ->assertSeeIn('#notifBadge', '1') // Jumlah notif 1
                    
                    // Klik icon lonceng
                    ->click('#notifWrapper button')
                    ->waitFor('#notifDropdown') // Dropdown muncul
                    ->assertSee('Your event documentation has been approved by admin.');
        });
    }

    /**
     * TC-PBI15-05: Organizer menekan tombol Mark as Read / mengklik notifikasi.
     */
    public function test_organizer_can_mark_notification_as_read()
    {
        $organizer = User::create([
            'name' => 'Test Org 2',
            'email' => 'org2'.uniqid().'@test.com',
            'password' => bcrypt('password'),
            'role' => 'organizer'
        ]);
        
        $notification = Notification::create([
            'user_id' => $organizer->id,
            'title' => 'Documentation Rejected',
            'message' => 'Please reupload',
            'type' => 'error',
            'is_read' => false
        ]);

        $this->browse(function (Browser $browser) use ($organizer, $notification) {
            $browser->loginAs($organizer)
                    ->visit('/dashboard')
                    ->pause(1500) // Wait for fetch
                    ->click('#notifWrapper button')
                    ->waitFor('#notifDropdown')
                    ->assertSee('Documentation Rejected')
                    
                    // Asumsikan menekan notifikasi akan men-trigger mark as read (via JS/Ajax)
                    ->click('#notifList > div') // Selector item notifikasi
                    ->pause(1500) // Tunggu request AJAX selesai
                    
                    // Refresh halaman atau cek UI (tergantung implementasi)
                    ->refresh()
                    ->pause(1500)
                    ->assertPresent('#notifBadge.hidden'); // Badge hilang karena is_read = true
                    
            // Verifikasi di database
            $this->assertDatabaseHas('notifications', [
                'id' => $notification->id,
                'is_read' => true
            ]);
        });
    }

    /**
     * TC-PBI15-06: Notifikasi hanya dikirimkan ke pembuat event terkait.
     */
    public function test_notification_is_isolated_to_the_correct_organizer()
    {
        $organizerA = User::create(['name' => 'Org A', 'email' => 'orga'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        $organizerB = User::create(['name' => 'Org B', 'email' => 'orgb'.uniqid().'@test.com', 'password' => bcrypt('password'), 'role' => 'organizer']);
        
        // Buat notifikasi khusus untuk organizer A
        Notification::create([
            'user_id' => $organizerA->id,
            'title' => 'Event A Approved',
            'message' => 'Good job!',
            'type' => 'success',
            'is_read' => false
        ]);

        $this->browse(function (Browser $browser) use ($organizerA, $organizerB) {
            // Organizer B tidak boleh melihat notifikasi Organizer A
            $browser->loginAs($organizerB)
                    ->visit('/dashboard')
                    ->pause(1000)
                    ->assertPresent('#notifBadge.hidden')
                    ->click('#notifWrapper button')
                    ->pause(500)
                    ->assertDontSee('Event A Approved');

            // Organizer A harusnya bisa melihat
            $browser->loginAs($organizerA)
                    ->visit('/dashboard')
                    ->pause(1500)
                    ->assertSeeIn('#notifBadge', '1');
        });
    }
}
