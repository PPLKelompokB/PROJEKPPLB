<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Documentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizerDocumentationController extends Controller
{
    public function index()
    {
        // Fetch completed events for the current organizer
        $events = Event::where('organizer_id', auth()->id())
            ->where('event_date', '<', now())
            ->withCount('documentations')
            ->latest('event_date')
            ->paginate(10);

        return view('organizer.documentation.index', compact('events'));
    }

    public function show($eventId)
    {
        // Fetch the specific completed event
        $event = Event::where('organizer_id', auth()->id())
            ->where('event_date', '<', now())
            ->with(['documentations' => function($q) {
                $q->latest();
            }])
            ->findOrFail($eventId);

        // Fetch all completed events for the dropdown
        $completedEvents = Event::where('organizer_id', auth()->id())
            ->where('event_date', '<', now())
            ->orderBy('event_date', 'desc')
            ->get();

        return view('organizer.documentation.show', compact('event', 'completedEvents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:10240', // 10MB
            'note' => 'nullable|string|max:1000'
        ]);

        $event = Event::findOrFail($request->event_id);

        if (auth()->id() !== $event->organizer_id) {
            abort(403, 'Unauthorized action.');
        }

        $filePath = $request->file('file')->store('documentations', 'public');

        Documentation::create([
            'event_id' => $event->id,
            'organizer_id' => $event->organizer_id,
            'file_path' => $filePath,
            'note' => $request->note,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Documentation uploaded successfully.');
    }

    public function destroy($id)
    {
        $documentation = Documentation::findOrFail($id);

        if (auth()->id() !== $documentation->organizer_id) {
            abort(403, 'Unauthorized action.');
        }

        if (Storage::disk('public')->exists($documentation->file_path)) {
            Storage::disk('public')->delete($documentation->file_path);
        }

        $documentation->delete();

        return back()->with('success', 'Documentation deleted successfully.');
    }
}
