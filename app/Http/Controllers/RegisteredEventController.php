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
            ->join('events', 'event_registrations.event_id', '=', 'events.id')
            ->where('event_registrations.user_id', $userId)
            ->select('event_registrations.*');

        // Search by event title
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('events.title', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $now = now();
            $status = $request->status;

            if ($status === 'cancelled') {
                $query->where('event_registrations.status', 'canceled');
            } elseif ($status === 'completed') {
                $query->where('event_registrations.status', '!=', 'canceled')
                      ->where('events.event_date', '<', $now);
            } elseif ($status === 'upcoming') {
                $query->where('event_registrations.status', '!=', 'canceled')
                      ->where('events.event_date', '>=', $now);
            } elseif ($status === 'registered') {
                $query->where('event_registrations.status', 'registered')
                      ->where('events.event_date', '>=', $now);
            }
        }

        // Sort direction
        $direction = $request->input('date', 'asc') === 'desc' ? 'desc' : 'asc';

        $registrations = $query
            ->orderBy('events.event_date', $direction)
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

    /**
     * Cancel the registration.
     */
    public function cancel($id)
    {
        $userId = auth()->id();

        $registration = EventRegistration::where('user_id', $userId)
            ->where('event_id', $id)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Registration not found.');
        }

        $registration->update(['status' => 'canceled']);

        return back()->with('success', 'Berhasil membatalkan pendaftaran event.');
    }
}
