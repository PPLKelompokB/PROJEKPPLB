<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function mark(Request $request, $registrationId)
    {
        $registration = \App\Models\EventRegistration::findOrFail($registrationId);

        $event = $registration->event;

        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $status = $request->input('status'); 

        if (!in_array($status, ['present', 'absent'])) {
            return back()->with('error', 'Status tidak valid');
        }

        \App\Models\Attendance::updateOrCreate(
            ['registration_id' => $registration->id],
            [
                'status' => $status,
                'marked_at' => now()
            ]
        );

        return back()->with('success', 'Status berhasil diupdate');
    }
}
