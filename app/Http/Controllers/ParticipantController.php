<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index($eventId)
    {
        $event = Event::with('registrations.user')->findOrFail($eventId);

        $attendances = Attendance::where('event_id', $eventId)
            ->get()
            ->keyBy('user_id'); 

        $data = $event->registrations->map(function ($registration) use ($attendances) {

            $attendance = $attendances->get($registration->user_id);

            return [
                'name' => $registration->user->name,
                'email' => $registration->user->email,
                'status_kehadiran' => $attendance 
                    ? $attendance->status 
                    : 'belum dikonfirmasi'
            ];
        });

        return response()->json([
            'event' => $event->title,
            'participants' => $data
        ]);
    }
}