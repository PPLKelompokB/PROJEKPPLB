<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventRegistration;
use App\Models\Event;

class RegisteredEventController extends Controller
{
    /**
     * Display a listing of the registered events.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        $query = EventRegistration::with('event.organizer')
            ->where('user_id', $userId)
            ->whereHas('event');

        // Search by event title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('event', fn($q) => $q->where('title', 'like', "%{$search}%"));
        }

        // Filter by status
        if ($request->filled('status')) {
            $now = now();
            $status = $request->status;

            if ($status === 'cancelled') {
                $query->where('status', 'cancelled');
            } elseif ($status === 'completed') {
                $query->where('status', '!=', 'cancelled')
                      ->whereHas('event', fn($q) => $q->where('event_date', '<', $now));
            } elseif ($status === 'upcoming') {
                $query->where('status', '!=', 'cancelled')
                      ->whereHas('event', fn($q) => $q->where('event_date', '>=', $now));
            } elseif ($status === 'registered') {
                $query->where('status', 'registered')
                      ->whereHas('event', fn($q) => $q->where('event_date', '>=', $now));
            }
        }

        // Sort direction
        $direction = $request->input('date', 'asc') === 'desc' ? 'desc' : 'asc';

        $registrations = $query
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->orderBy('events.event_date', $direction)
            ->select('event_registrations.*')
            ->paginate(10)
            ->withQueryString();

        return view('volunteer.registered-events.index', compact('registrations'));
    }

    /**
     * Display the specified registered event detail.
     */
    public function show($id)
    {
        $userId = auth()->id();

        // Get the registration, verify it belongs to user, verify event exists
        $registration = EventRegistration::with('event.organizer')
            ->where('user_id', $userId)
            ->where('event_id', $id)
            ->first();

        // If not found or event deleted
        if (!$registration || !$registration->event) {
            abort(404, 'Event tidak ditemukan.');
        }

        return view('volunteer.registered-events.show', compact('registration'));
    }
}
