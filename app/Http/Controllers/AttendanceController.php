<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Point;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function mark(Request $request, $registrationId)
    {
        $registration = EventRegistration::findOrFail($registrationId);
        $event = $registration->event;

        // 🔒 hanya organizer event
        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $status = $request->input('status');

        if (!in_array($status, ['present', 'absent'])) {
            return back()->with('error', 'Status tidak valid');
        }

        // ✅ simpan attendance
        $attendance = Attendance::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'user_id' => $registration->user_id,
                'event_id' => $event->id,
                'status' => $status,
                'marked_at' => now()
            ]
        );

        // ========================
        // 🔥 HITUNG POINT
        // ========================
        if (!$attendance->is_counted && $status === 'present') {

            $userId = $registration->user_id;

            // contoh: 1 jam = 10 poin
            $pointsEarned = $event->duration * 10;

            // simpan ke tabel points
            Point::create([
                'user_id' => $userId,
                'event_id' => $event->id,
                'points' => $pointsEarned
            ]);

            // update total poin user
            $user = User::find($userId);
            $user->points += $pointsEarned;
            $user->save();

            // tandai sudah dihitung
            $attendance->is_counted = true;
            $attendance->save();
        }

        return back()->with('success', 'Status berhasil diupdate');
    }
}