<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with(['organizer', 'registrations.user'])
            ->find($id);

        if (!$event) {
            return redirect('/')->with('error', 'Event tidak ditemukan');
        }

        $user = auth()->user();

        $totalVolunteers = $event->registrations->count();

        $isRegistered = false;

        if ($user) {
            $isRegistered = $event->registrations
                ->where('user_id', $user->id)
                ->where('status', 'registered')
                ->isNotEmpty();
        }

        $isFull = $totalVolunteers >= $event->quota;

        return view('events.detail', [
            'event' => $event,
            'totalVolunteers' => $totalVolunteers,
            'isRegistered' => $isRegistered,
            'isFull' => $isFull,
        ]);
    }

    public function manage()
    {
        $events = Event::where('organizer_id', auth()->id())
            ->withCount('registrations')
            ->latest()
            ->paginate(6);

        return view('events.manage', compact('events'));
    }

    public function detail($id)
    {
        $event = Event::where('organizer_id', auth()->id())
            ->with('organizer')
            ->findOrFail($id);

        return view('events.detail-organizer', compact('event'));
    }

    public function edit($id)
    {
        $event = Event::where('organizer_id', auth()->id())
            ->findOrFail($id);

        return view('events.edit', compact('event'));
    }

    public function participants($id)
    {
        $event = Event::with(['registrations.user'])->findOrFail($id);

        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $participants = $event->registrations()
            ->with('user')
            ->latest()
            ->paginate(5);

        $attendanceMap = \App\Models\Attendance::where('event_id', $event->id)
            ->get()
            ->keyBy('user_id');

        $allEvents = \App\Models\Event::where('organizer_id', auth()->id())
            ->latest()
            ->get(['id', 'title', 'event_date']);

        return view('events.participants', [
            'event' => $event,
            'participants' => $participants,
            'attendanceMap' => $attendanceMap,
            'allEvents' => $allEvents
        ]);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:1|max:12',
            'quota' => 'required|integer|min:1',
            'description' => 'required|string',

            'meeting_point' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',

            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data['event_date'] = $data['date'] . ' ' . $data['time'];

        if ($request->hasFile('image')) {

            // hapus lama (optional)
            if ($event->image) {
                Storage::delete($event->image);
            }

            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        $data['contact_phone'] = $data['phone'] ?? null;

        unset($data['date'], $data['time'], $data['phone']);

        $event->update($data);

        return redirect()
            ->route('events.detail', $event->id)
            ->with('success', 'Event berhasil diupdate');
    }
}