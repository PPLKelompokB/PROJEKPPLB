<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index($eventId)
    {
        $event = Event::with([
            'registrations.user',
            'registrations.attendance'
        ])->findOrFail($eventId);

        $participants = $event->registrations()
        ->with(['user', 'attendance'])
        ->latest()
        ->paginate(5);
        
        $data = $event->registrations->map(function ($registration) {
            return [
                'name' => $registration->user->name ?? 'Unknown',
                'email' => $registration->user->email ?? '-',
                'status_kehadiran' => $registration->attendance->status ?? 'belum dikonfirmasi'
            ];
        });

        return response()->json([
            'event' => $event->title,
            'participants' => $data
        ]);
    }
}