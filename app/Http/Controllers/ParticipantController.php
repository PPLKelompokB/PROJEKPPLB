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
            'attendances'
        ])->findOrFail($eventId);

        $data = $event->registrations->map(function ($registration) use ($event) {

            $attendance = $event->attendances
                ->where('user_id', $registration->user_id)
                ->first();

            return [
                'name' => $registration->user->name ?? 'Unknown',
                'email' => $registration->user->email ?? '-',
                'status_kehadiran' => $attendance ? $attendance->status : 'belum dikonfirmasi'
            ];
        });

        return response()->json([
            'event' => $event->title,
            'participants' => $data
        ]);
    }
}