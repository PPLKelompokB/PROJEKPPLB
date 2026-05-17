<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\User;
use App\Models\EventRegistration;
use App\Models\Attendance;
use App\Models\Point;

class DashboardController extends Controller
{
    // ========================
    // VOLUNTEER
    // ========================
    public function volunteer()
    {
        $user = Auth::user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->get();

        $upcomingEvents = $registrations->filter(fn($r) =>
            $r->event && $r->event->event_date > now()
        );

        $history = $registrations->filter(fn($r) =>
            $r->event && $r->event->event_date <= now()
        );

        // 🔥 FIX STATUS → present
        $totalHours = Attendance::where('user_id', $user->id)
            ->where('status', 'present')
            ->count() * 2;

        $totalPoints = $user->points ?? 0;

        $pointHistory = Point::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard.volunteer.dashboard', [
            'user' => $user,
            'totalEvents' => $registrations->count(),
            'upcomingEvents' => $upcomingEvents,
            'history' => $history,
            'totalHours' => $totalHours,
            'totalPoints' => $totalPoints,
            'pointHistory' => $pointHistory,
        ]);
    }

    // ========================
    // ORGANIZER
    // ========================
    public function organizer()
    {
        $user = Auth::user();

        $allEvents = Event::with('registrations')
            ->where('organizer_id', $user->id)
            ->latest()
            ->get();
            
        $events = Event::with('registrations')
            ->where('organizer_id', $user->id)
            ->latest()
            ->paginate(5);

        return view('dashboard.organizer.dashboard', [
            'events' => $events,
            'totalEvents' => $allEvents->count(),
            'totalVolunteers' => EventRegistration::whereIn('event_id', $allEvents->pluck('id'))->count(),
            'activeEvents' => $allEvents->filter(function($e) {
                $start = \Carbon\Carbon::parse($e->event_date);
                $end = $start->copy()->addHours($e->duration);
                return now() >= $start && now() <= $end;
            })->count(),
        ]);
    }

    // ========================
    // ADMIN
    // ========================
    public function admin()
    {
        return view('dashboard.admin.dashboard', [
            'events' => Event::latest()->paginate(10),
            'totalUsers' => User::count(),
            'totalEvents' => Event::count(),
            'finishedEvents' => Event::where('event_date', '<', now())->count(),
        ]);
    }
}