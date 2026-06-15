<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LeaderboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    // ==========================================
    // LDB-TC01: Leaderboard Volunteer (Summary)
    // ==========================================
    public function test_LDB_TC01_view_summary_leaderboard()
    {
        $viewer = User::factory()->create(['role' => 'volunteer']);
        User::factory()->count(5)->create(['role' => 'volunteer']);

        $this->browse(function (Browser $browser) use ($viewer) {
            $browser->visit('/login')
                    ->type('email', $viewer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    ->visit('/leaderboard')
                    ->pause(2000)
                    ->assertPathIs('/leaderboard')
                    ->assertSee('Volunteer Leaderboard')
                    ->assertSee('Top Volunteers')
                    ->assertSee('Your Rank')
                    ->assertSee('Achievements')
                    ->assertSee('Quick Stats');
            $rows = $browser->elements('table tbody tr');
            $this->assertCount(5, $rows, 'Tabel tidak berisi tepat 5 baris data sesuai ekspektasi.');
        });
    }

    // ==========================================
    // LDB-TC02: Akumulasi Poin pada Widget
    // ==========================================
    public function test_LDB_TC02_verify_your_rank_widget()
    {
        $v1 = User::factory()->create(['role' => 'volunteer', 'points' => 100]);
        User::factory()->count(4)->create(['role' => 'volunteer', 'points' => 10]);
        
        $event = Event::factory()->create(['status' => 'published']);
        Attendance::create([
            'user_id' => $v1->id,
            'event_id' => $event->id,
            'status' => 'present'
        ]);

        $this->browse(function (Browser $browser) use ($v1) {
            $browser->visit('/login')
                    ->type('email', $v1->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    ->visit('/leaderboard')
                    ->pause(2000)
                    ->assertSee('100')
                    ->assertSee('1'); 
        });
    }

    // ==========================================
    // LDB-TC03: Sorting Descending di Full Leaderboard
    // ==========================================
    public function test_LDB_TC03_sorting_descending_full_leaderboard()
    {
        $menang = User::factory()->create(['name' => 'Menang Poin', 'role' => 'volunteer', 'points' => 100]);
        User::factory()->create(['name' => 'Kalah Poin', 'role' => 'volunteer', 'points' => 10]);

        $this->browse(function (Browser $browser) use ($menang) {
            $browser->visit('/login')
                    ->type('email', $menang->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    ->visit('/leaderboard/full?sort=desc')
                    ->pause(2000);
            $html = $browser->driver->getPageSource();
            $posMenang = strpos($html, 'Menang Poin');
            $posKalah = strpos($html, 'Kalah Poin');
            
            $this->assertTrue($posMenang !== false && $posKalah !== false, 'Data volunteer tidak ter-render di layar');
            $this->assertTrue($posMenang < $posKalah, 'Error Sorting: Kalah Poin muncul di atas Menang Poin!');
        });
    }

    // ==========================================
    // LDB-TC04: Unauthenticated Access (Guest)
    // ==========================================
    public function test_LDB_TC04_unauthenticated_access()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout() 
                    ->pause(1000)
                    ->visit('/leaderboard')
                    ->pause(1500)
                    ->assertPathIs('/login')
                    ->visit('/leaderboard/full')
                    ->pause(1500)
                    ->assertPathIs('/login');
        });
    }

    // ==========================================
    // LDB-TC05: Kalkulasi Data Relawan Baru
    // ==========================================
    public function test_LDB_TC05_new_volunteer_calculation()
    {
        $newVolunteer = User::factory()->create(['role' => 'volunteer', 'points' => 0]);

        $this->browse(function (Browser $browser) use ($newVolunteer) {
            $browser->visit('/login')
                    ->type('email', $newVolunteer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    ->visit('/leaderboard')
                    ->pause(2000)
                    ->assertPathIs('/leaderboard')
                    ->assertSee('0'); 
        });
    }

// ==========================================
    // LDB-TC06: Navigasi Pagination Full Leaderboard
    // ==========================================
    public function test_LDB_TC06_full_leaderboard_pagination()
    {
        $viewer = User::factory()->create(['role' => 'volunteer', 'points' => 200]);
        for ($i = 0; $i < 28; $i++) {
            User::factory()->create([
                'role' => 'volunteer', 
                'points' => 120 - $i 
            ]);
        }
        
        User::factory()->create(['name' => 'Relawan Paling Bawah', 'role' => 'volunteer', 'points' => 0]);

        $this->browse(function (Browser $browser) use ($viewer) {
            $browser->visit('/login')
                    ->type('email', $viewer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    
                    ->visit('/leaderboard/full')
                    ->pause(2000)
                    ->assertDontSee('Relawan Paling Bawah')
                    
                    // Navigasi ke halaman 2
                    ->visit('/leaderboard/full?page=2')
                    ->pause(2000)
                    ->assertSee('Relawan Paling Bawah');
        });
    }
}