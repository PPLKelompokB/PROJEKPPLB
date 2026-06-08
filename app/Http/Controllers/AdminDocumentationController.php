<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Documentation;
use Illuminate\Http\Request;

class AdminDocumentationController extends Controller
{
    public function index()
    {
        // Get events that have documentations attached
        $events = Event::whereHas('documentations')
            ->with(['organizer', 'documentations' => function($q) {
                $q->latest();
            }])
            ->latest()
            ->paginate(10);

        return view('admin.documentation.index', compact('events'));
    }

    public function show($eventId)
    {
        $event = Event::with(['organizer', 'documentations'])->findOrFail($eventId);
        
        return view('admin.documentation.show', compact('event'));
    }
}
