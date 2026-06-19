<?php

namespace Tests\Browser;

use App\Models\Event;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ManageEventTest extends DuskTestCase
{
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function makeOrganizer(string $suffix = ''): User
    {
        return User::create([
            'name'     => 'Test Organizer ' . $suffix . uniqid(),
            'email'    => 'organizer' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'role'     => 'organizer',
        ]);
    }

    private function makeEvent(User $organizer, array $overrides = []): Event
    {
        return Event::create(array_merge([
            'organizer_id' => $organizer->id,
            'title'        => 'Test Event ' . uniqid(),
            'description'  => 'Test description for event.',
            'location'     => 'Pantai Kuta, Bali',
            'event_date'   => now()->addDays(10)->format('Y-m-d H:i:s'),
            'duration'     => 3,
            'quota'        => 50,
            'status'       => 'published',
            'meeting_point' => 'Main Gate',
        ], $overrides));
    }

    // ─── KBP10-TC01: Melihat daftar event melalui Dashboard ───────────────────

    public function test_KBP10_TC01_view_event_list_on_dashboard()
    {
        $organizer = $this->makeOrganizer('TC01');
        $event = $this->makeEvent($organizer, ['title' => 'Pantai Cleanup TC01 Dashboard']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/organizer/dashboard')
                    ->assertSee('Organizer Dashboard')
                    ->assertSee($event->title);
        });
    }

    // ─── KBP10-TC02: Melihat daftar event melalui halaman Manage Event ────────

    public function test_KBP10_TC02_view_event_list_on_manage_event()
    {
        $organizer = $this->makeOrganizer('TC02');
        $event = $this->makeEvent($organizer, ['title' => 'Pantai Cleanup TC02 Manage Event']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee('Manage Your Events')
                    ->assertSee($event->title);
        });
    }

    // ─── KBP10-TC03: Mengubah data event melalui Dashboard ────────────────────

    public function test_KBP10_TC03_edit_event_from_dashboard()
    {
        $organizer = $this->makeOrganizer('TC03');
        $event = $this->makeEvent($organizer, ['title' => 'Event Before Edit TC03']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $date = now()->addDays(20)->format('Y-m-d');

            // 1. Login dan buka Dashboard
            $browser->loginAs($organizer)
                    ->visit('/organizer/dashboard')
                    ->assertSee($event->title);

            // 2. Klik ikon Edit pada event yang dipilih
            $browser->click('a[href="/events/' . $event->id . '/edit"]')
                    ->waitForText('Edit Event', 10)
                    ->assertSee('Edit Event');

            // 3. Ubah data event
            $browser->clear('title')
                    ->type('title', 'Event After Edit TC03')
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '10:00';"
                    ]);

            // 4. Klik Update Event
            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil diupdate', 10)
                    ->assertSee('Event berhasil diupdate');

            // Verifikasi perubahan di DB
            $this->assertDatabaseHas('events', [
                'id'    => $event->id,
                'title' => 'Event After Edit TC03',
            ]);
        });
    }

    // ─── KBP10-TC04: Mengubah data event melalui halaman Manage Event ─────────

    public function test_KBP10_TC04_edit_event_from_manage_event()
    {
        $organizer = $this->makeOrganizer('TC04');
        $event = $this->makeEvent($organizer, ['title' => 'Event Before Edit TC04']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            $date = now()->addDays(25)->format('Y-m-d');

            // 1. Login dan buka Manage Event
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee($event->title);

            // 2. Klik View Details pada event yang dipilih
            $browser->click('a[href="/events/' . $event->id . '"]')
                    ->waitForText($event->title, 10);

            // 3. Klik Edit Event
            $browser->click('a[href="' . route('events.edit', $event->id) . '"]')
                    ->waitForText('Edit Event', 10)
                    ->assertSee('Edit Event');

            // 4. Ubah data event
            $browser->clear('title')
                    ->type('title', 'Event After Edit TC04')
                    ->script([
                        "document.querySelector('input[name=date]').value = '{$date}';",
                        "document.querySelector('input[name=time]').value = '11:00';"
                    ]);

            // 5. Klik Update Event
            $browser->click('button[name="action"][value="publish"]')
                    ->waitForText('Event berhasil diupdate', 10)
                    ->assertSee('Event berhasil diupdate');

            // Verifikasi perubahan di DB
            $this->assertDatabaseHas('events', [
                'id'    => $event->id,
                'title' => 'Event After Edit TC04',
            ]);
        });
    }

    // ─── KBP10-TC05: Menghapus event melalui Dashboard ────────────────────────

    public function test_KBP10_TC05_delete_event_from_dashboard()
    {
        $organizer = $this->makeOrganizer('TC05');
        $event = $this->makeEvent($organizer, ['title' => 'Event To Delete TC05']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            // 1. Login dan buka Dashboard
            $browser->loginAs($organizer)
                    ->visit('/organizer/dashboard')
                    ->assertSee($event->title);

            // 2. Klik ikon Delete pada event yang dipilih
            $browser->click('.delete-form button[type="button"]')
                    ->waitForText('Are you sure you want to delete this event?', 5);

            // 3. Klik Confirm Delete
            $browser->click('#dashboardDeleteModal button[onclick="confirmDashboardDelete()"]')
                    ->waitForText('Event berhasil dihapus', 10)
                    ->assertSee('Event berhasil dihapus');

            // Verifikasi event terhapus dari DB
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
    }

    // ─── KBP10-TC06: Menghapus event melalui halaman Manage Event ─────────────

    public function test_KBP10_TC06_delete_event_from_manage_event()
    {
        $organizer = $this->makeOrganizer('TC06');
        $event = $this->makeEvent($organizer, ['title' => 'Event To Delete TC06']);

        $this->browse(function (Browser $browser) use ($organizer, $event) {
            // 1. Login dan buka Manage Event
            $browser->loginAs($organizer)
                    ->visit('/events/manage')
                    ->assertSee($event->title);

            // 2. Klik View Details
            $browser->click('a[href="/events/' . $event->id . '"]')
                    ->waitForText($event->title, 10);

            // 3. Klik Delete Event
            $browser->click('#deleteEventForm button[type="button"]')
                    ->waitForText('Are you sure you want to delete this event?', 5);

            // 4. Klik Confirm Delete
            $browser->click('#deleteModal button[onclick="submitDelete()"]')
                    ->waitForText('Event berhasil dihapus', 10)
                    ->assertSee('Event berhasil dihapus');

            // Verifikasi event terhapus dari DB
            $this->assertDatabaseMissing('events', ['id' => $event->id]);
        });
    }
}
