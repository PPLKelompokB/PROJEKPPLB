<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing', [
            'events' => Event::latest()->take(6)->get(),
            'totalEvents' => Event::count(),
            'totalVolunteers' => User::where('role', 'volunteer')->count(),
        ]);
    }
}