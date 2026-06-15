<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Event;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KBP10Test extends DuskTestCase
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function makeOrganizer(): User
    {
        return User::create([
            'name'     => 'Organizer KBP10 ' . uniqid(),
            'email'    => 'org' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);
    }

    private function makeEvent(User $organizer, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'organizer_id'  => $organizer->id,
            'title'         => 'Test Event ' . uniqid(),
            'description'   => 'Deskripsi event untuk testing.',
            'location'      => 'Pantai Kuta, Bali',
            'event_date'    => now()->addDays(10)->format('Y-m-d H:i:s'),
            'duration'      => 3,
            'quota'         => 50,
            'status'        => 'published',
            'meeting_point' => 'Main Gate',
        ], $overrides));
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  KBP10-TC01: Melihat daftar event milik organizer
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP10-TC01: Organizer melihat daftar event miliknya di Manage Event
     */
    public function test_KBP10_TC01_view_event_list_on_manage_event()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Pantai Cleanup KBP10-TC01']);

        $this->browse(function (Browser $browser) use ($organizer) {
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('Manage Your Events')
                    ->assertSee('Pantai Cleanup KBP10-TC01');
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  KBP10-TC02: Mengubah data event melalui Dashboard
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP10-TC02: Organizer edit event dari Dashboard → klik ikon Edit → ubah → Update Event
     */
    public function test_KBP10_TC02_edit_event_from_dashboard()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event Before Edit TC02']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $date = now()->addDays(20)->format('Y-m-d');

            // 1. Login dan buka Dashboard
            $browser->loginAs($organizer)
                    ->visit('/organizer/dashboard')
                    ->assertSee('Event Before Edit TC02');

            // 2. Klik ikon Edit pada event
            $browser->click('a[href="/events/' . $event->id . '/edit"]')
                    ->waitForText('Edit Event', 10)
                    ->assertSee('Edit Event');

            // 3. Ubah data event
            $browser->clear('title')
                    ->type('title', 'Event After Edit TC02')
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '10:00';"
                    ]);

            // 4. Klik Update Event
            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil diupdate', 10)
                    ->assertSee('Event berhasil diupdate');

            // Verifikasi di DB
            $this->assertDatabaseHas('events', [
                'id'    => $event->id,
                'title' => 'Event After Edit TC02',
            ]);
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  KBP10-TC03: Mengubah data event melalui Manage Event
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP10-TC03: Organizer edit event dari Manage Event → View Details → Edit Event → Update
     */
    public function test_KBP10_TC03_edit_event_from_manage_event()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event Before Edit TC03']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $date = now()->addDays(25)->format('Y-m-d');

            // 1. Login dan buka Manage Event
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('Event Before Edit TC03');

            // 2. Klik View Details
            $browser->click('a[href="/events/' . $event->id . '"]')
                    ->waitForText($event->title, 10);

            // 3. Klik Edit Event
            $browser->click('a[href="' . route('events.edit', $event->id) . '"]')
                    ->waitForText('Edit Event', 10)
                    ->assertSee('Edit Event');

            // 4. Ubah data event
            $browser->clear('title')
                    ->type('title', 'Event After Edit TC03')
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '11:00';"
                    ]);

            // 5. Klik Update Event
            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil diupdate', 10)
                    ->assertSee('Event berhasil diupdate');

            // Verifikasi di DB
            $this->assertDatabaseHas('events', [
                'id'    => $event->id,
                'title' => 'Event After Edit TC03',
            ]);
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  KBP10-TC04: Menghapus event melalui Dashboard
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP10-TC04: Organizer hapus event dari Dashboard → klik Delete → Confirm Delete
     */
    public function test_KBP10_TC04_delete_event_from_dashboard()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event To Delete TC04']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            // 1. Login dan buka Dashboard
            $browser->loginAs($organizer)
                    ->visit('/organizer/dashboard')
                    ->assertSee('Event To Delete TC04');

            // 2. Klik ikon Delete pada event → modal muncul
            $browser->click('.delete-form button[type="button"]')
                    ->waitForText('Are you sure you want to delete this event?', 5)
                    ->assertSee('Are you sure you want to delete this event?');

            // 3. Klik Confirm Delete
            $browser->click('#dashboardDeleteModal button[onclick="confirmDashboardDelete()"]')
                    ->waitForText('Event berhasil dihapus', 10)
                    ->assertSee('Event berhasil dihapus');

            // Verifikasi event terhapus dari DB
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  KBP10-TC05: Menghapus event melalui Manage Event
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * KBP10-TC05: Organizer hapus event dari Manage Event → View Details → Delete Event → Confirm Delete
     */
    public function test_KBP10_TC05_delete_event_from_manage_event()
    {
        $organizer = $this->makeOrganizer();
        $event     = $this->makeEvent($organizer, ['title' => 'Event To Delete TC05']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            // 1. Login dan buka Manage Event
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('Event To Delete TC05');

            // 2. Klik View Details
            $browser->click('a[href="/events/' . $event->id . '"]')
                    ->waitForText($event->title, 10);

            // 3. Klik Delete Event (tombol Hapus Event di detail page)
            $browser->click('#deleteEventForm button[type="button"]')
                    ->waitForText('Are you sure you want to delete this event?', 5)
                    ->assertSee('Are you sure you want to delete this event?');

            // 4. Klik Confirm Delete (tombol Hapus Event di modal)
            $browser->click('#deleteModal button[onclick="submitDelete()"]')
                    ->waitForText('Event berhasil dihapus', 10)
                    ->assertSee('Event berhasil dihapus');

            // Verifikasi event terhapus dari DB
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
    }
}
