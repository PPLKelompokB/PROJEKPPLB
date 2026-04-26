<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        $events = Event::with('registrations')
            ->where('status', 'published')
            ->latest('event_date')
            ->take(3)
            ->get();

        $totalVolunteers = User::where('role', 'volunteer')->count();
        $totalEvents = Event::count();

        return view('landing', compact(
            'events',
            'totalVolunteers',
            'totalEvents'
        ));
    }
}