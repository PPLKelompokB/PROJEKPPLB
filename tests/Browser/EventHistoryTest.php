<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\EventRegistration;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EventHistoryTest extends DuskTestCase
{

    // ==========================================
    // HIS-TC01: Tampil Daftar History & Statusnya
    // ==========================================
    public function test_HIS_TC01_view_history_list()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);
        
        $eventHadir = Event::factory()->create(['title' => 'Malibu Coastal Restoration', 'status' => 'published', 'event_date' => now()->subDays(5)]);
        $eventBolos = Event::factory()->create(['title' => 'Suramadu View Point Restoration', 'status' => 'published', 'event_date' => now()->subDays(3)]);

        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventHadir->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventBolos->id, 'status' => 'registered']);

        Attendance::create(['user_id' => $volunteer->id, 'event_id' => $eventHadir->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->visit('/login')
                    ->type('email', $volunteer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    
                    ->visit('/history')
                    ->pause(2500)
                    ->assertPathIs('/history') 
                    ->assertSee('Malibu Coastal Restoration')
                    ->assertSee('Present')
                    ->assertSee('10 Points Earned')
                    ->assertSee('Suramadu View Point Restoration')
                    ->assertSee('Absent')
                    ->assertSee('0 Points');
        });
    }

    // ==========================================
    // HIS-TC02: Fitur Pencarian (Search)
    // ==========================================
    public function test_HIS_TC02_search_event()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);
        
        $eventSura = Event::factory()->create(['title' => 'Suramadu Restoration', 'status' => 'published', 'event_date' => now()->subDays(4)]);
        $eventKuta = Event::factory()->create(['title' => 'Pantai Kuta Cleanup', 'status' => 'published', 'event_date' => now()->subDays(2)]);

        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventSura->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventKuta->id, 'status' => 'registered']);

        Attendance::create(['user_id' => $volunteer->id, 'event_id' => $eventSura->id, 'status' => 'present']);
        Attendance::create(['user_id' => $volunteer->id, 'event_id' => $eventKuta->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->visit('/login')
                    ->type('email', $volunteer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    
                    ->visit('/history')
                    ->pause(2000) 
                    ->assertSee('Suramadu Restoration')
                    ->assertSee('Pantai Kuta Cleanup') 
                    ->pause(1500)
                    
                    ->type('search', 'sura') 
                    ->keys('input[name="search"]', '{enter}')
                    ->pause(3000) 
                    
                    ->assertSee('Suramadu Restoration')
                    ->assertDontSee('Pantai Kuta Cleanup'); 
        });
    }

    // ==========================================
    // HIS-TC03: Fitur Filter Dropdown Tahun
    // ==========================================
    public function test_HIS_TC03_filter_by_year()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);
        
        $event2025 = Event::factory()->create(['title' => 'Event Tahun Lalu', 'status' => 'published', 'event_date' => '2025-05-10 10:00:00']);
        $event2026 = Event::factory()->create(['title' => 'Event Tahun Ini', 'status' => 'published', 'event_date' => '2026-05-10 10:00:00']);

        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event2025->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $event2026->id, 'status' => 'registered']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->visit('/login')
                    ->type('email', $volunteer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    
                    ->visit('/history?year=2025')
                    ->pause(3000) 
                    ->assertPathIs('/history')
                    
                    ->assertSee('Event Tahun Lalu')
                    ->assertDontSee('Event Tahun Ini'); 
        });
    }

    // ==========================================
    // HIS-TC04: Keamanan (Guest tidak bisa masuk)
    // ==========================================
    public function test_HIS_TC04_unauthenticated_access()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->pause(1500) 
                    
                    ->visit('/history')
                    ->pause(1500) 
                    ->assertPathIs('/login'); 
        });
    }

    // ==========================================
    // HIS-TC05: Validasi UI Empty State
    // ==========================================
    public function test_HIS_TC05_empty_state_validation()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->visit('/login')
                    ->type('email', $volunteer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    
                    ->visit('/history')
                    ->pause(2000) 
                    ->assertPathIs('/history')
                    ->assertSee('No history yet')
                    ->assertSee('You have never participated in an event that has already been completed.');
        });
    }

    // ==========================================
    // HIS-TC06: Validasi Hak Akses Download Sertifikat
    // ==========================================
    public function test_HIS_TC06_certificate_button_validation()
    {
        $volunteer = User::factory()->create(['role' => 'volunteer']);
        
        $eventHadir = Event::factory()->create(['title' => 'Event Hadir', 'status' => 'published', 'event_date' => now()->subDays(5)]);
        $eventBolos = Event::factory()->create(['title' => 'Event Bolos', 'status' => 'published', 'event_date' => now()->subDays(3)]);

        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventHadir->id, 'status' => 'registered']);
        EventRegistration::create(['user_id' => $volunteer->id, 'event_id' => $eventBolos->id, 'status' => 'registered']);
        Attendance::create(['user_id' => $volunteer->id, 'event_id' => $eventHadir->id, 'status' => 'present']);

        $this->browse(function (Browser $browser) use ($volunteer) {
            $browser->visit('/login')
                    ->type('email', $volunteer->email)
                    ->type('password', 'password')
                    ->press('Login')
                    ->pause(1500)
                    
                    ->visit('/history')
                    ->pause(2000)
                    ->assertPresent('a[title="Download Certificate"]')
                    ->assertPresent('button[disabled]');
        });
    }
}