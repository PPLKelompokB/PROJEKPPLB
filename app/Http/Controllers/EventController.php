<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with('organizer')->find($id);

        if (!$event) {
            return redirect('/')->with('error', 'Event tidak ditemukan');
        }

        return view('events.detail', compact('event'));
    }
}