<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\User;
use App\Models\EventRegistration;
use App\Models\Attendance;

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

        $totalHours = Attendance::where('user_id', $user->id)
            ->where('status', 'hadir')
            ->count() * 2;

        return view('dashboard.volunteer.dashboard', [
            'user' => $user,
            'totalEvents' => $registrations->count(),
            'upcomingEvents' => $upcomingEvents,
            'history' => $history,
            'totalHours' => $totalHours,
        ]);
    }

    // ========================
    // ORGANIZER
    // ========================
    public function organizer()
    {
        $user = Auth::user();

        $events = Event::with('registrations')
            ->where('organizer_id', $user->id)
            ->latest()
            ->get();

        return view('dashboard.organizer.dashboard', [
            'events' => $events,
            'totalEvents' => $events->count(),
            'totalVolunteers' => EventRegistration::whereIn('event_id', $events->pluck('id'))->count(),
            'activeEvents' => $events->where('event_date', '>', now())->count(),
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