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
            [
                'user_id' => $registration->user_id,
                'event_id' => $event->id
            ],
            [
                'status' => $status
            ]
        );

        // ========================
        // 🔥 HITUNG POINT (MOVED TO DOCUMENTATION VERIFICATION)
        // ========================
        if (!$attendance->is_counted && $status === 'present') {
            // tandai sudah dihitung
            $attendance->is_counted = true;
            $attendance->save();
        }

        return back()->with('success', 'Status berhasil diupdate');
    }
}