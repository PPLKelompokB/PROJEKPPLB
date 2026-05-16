<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\EventRegistration;

class EventController extends Controller
{   
    public function index(Request $request)
    {
        $query = Event::with('organizer')->withCount('registrations')->where('status', 'published');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('date')) {
            if ($request->date === 'today') {
                $query->whereDate('event_date', \Carbon\Carbon::today());
            } elseif ($request->date === 'this_week') {
                $query->whereBetween('event_date', [
                    \Carbon\Carbon::now()->startOfWeek(),
                    \Carbon\Carbon::now()->endOfWeek()
                ]);
            } elseif ($request->date === 'this_month') {
                $query->whereMonth('event_date', \Carbon\Carbon::now()->month)
                      ->whereYear('event_date', \Carbon\Carbon::now()->year);
            }
        }

        if ($request->filled('sort')) {
            if ($request->sort === 'latest') {
                $query->latest('event_date');
            } else {
                $query->oldest('event_date');
            }
        } else {
            $query->oldest('event_date');
        }

        $events = $query->paginate(6)->withQueryString();

        return view('events.index', compact('events'));
    }

    public function show($id)
    {
        $event = Event::with(['organizer', 'registrations.user'])
            ->find($id);

        if (!$event) {
            return redirect('/')->with('error', 'Event tidak ditemukan');
        }

        if ($event->status === 'draft' && (!auth()->check() || auth()->id() !== $event->organizer_id)) {
            abort(403, 'Unauthorized action.');
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
            ->with(['organizer', 'registrations.user'])
            ->findOrFail($id);

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

        return view('events.detail', compact('event', 'totalVolunteers', 'isRegistered', 'isFull'));
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

            if ($event->image) {
                Storage::delete($event->image);
            }

            $path = $request->file('image')->store('events', 'public');
            $data['image'] = $path;
        }

        $data['contact_phone'] = $data['phone'] ?? null;
        
        $data['status'] = $request->input('action') === 'draft' ? 'draft' : 'published';

        unset($data['date'], $data['time'], $data['phone']);

        $event->update($data);

        $message = $data['status'] === 'draft' ? 'Event berhasil disimpan sebagai draft' : 'Event berhasil diupdate';

        return redirect()
            ->route('events.detail', $event->id)
            ->with('success', $message);
    }

    public function register($id)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $event = Event::findOrFail($id);

        $alreadyRegistered = EventRegistration::where('user_id', $user->id)
            ->where('event_id', $id)
            ->where('status', 'registered')
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'Kamu sudah terdaftar di event ini');
        }

        if ($event->registrations()->count() >= $event->quota) {
            return back()->with('error', 'Kuota event sudah penuh');
        }

        if ($event->event_date < now()) {
            return back()->with('error', 'Event sudah selesai');
        }

        EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $id,
            'status' => 'registered'
        ]);

        return redirect()->route('events.show', $id)
            ->with('success', 'Berhasil mendaftar!');
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',

            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer|min:1|max:12',
            'quota' => 'required|integer|min:1',

            'meeting_point' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',

            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $data['event_date'] = $data['date'] . ' ' . $data['time'];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $data['organizer_id'] = auth()->id();

        $data['contact_phone'] = $data['phone'] ?? null;

        $data['status'] = $request->input('action') === 'draft' ? 'draft' : 'published';

        unset($data['date'], $data['time'], $data['phone']);

        $event = Event::create($data);

        $message = $data['status'] === 'draft' ? 'Event berhasil disimpan sebagai draft!' : 'Event berhasil dibuat!';

        return redirect()
            ->route('events.show', $event->id)
            ->with('success', $message);
    }
}