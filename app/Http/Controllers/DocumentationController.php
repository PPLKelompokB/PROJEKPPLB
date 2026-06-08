<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentation;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Point;
use App\Models\User;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class DocumentationController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Validasi sesuai PBI
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:10240',
            'note' => 'nullable|string|max:255'
        ]);

        $event = Event::findOrFail($request->event_id);

        // ✅ Authorization (biar tidak sembarang upload)
        if (auth()->id() !== $event->organizer_id) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Tidak diizinkan upload dokumentasi untuk event ini'], 403);
            }
            return back()->withErrors('Tidak diizinkan upload dokumentasi untuk event ini');
        }

        // ✅ Upload file beneran
        $filePath = $request->file('file')->store('documentations', 'public');

        // ✅ Simpan ke database
        $documentation = Documentation::create([
            'event_id' => $event->id,
            'organizer_id' => $event->organizer_id,
            'file_path' => $filePath,
            'note' => $request->note,
            'status' => 'pending'
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Dokumentasi berhasil diupload',
                'data' => $documentation
            ], 201);
        }

        return back()->with('success', 'Documentation uploaded successfully.');
    }

    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(
                Documentation::with('event')->latest()->get()
            );
        }

        $events = Event::where('organizer_id', auth()->id())->get();
        $documentations = Documentation::with('event')->where('organizer_id', auth()->id())->latest()->get();

        return view('organizer.documentation.upload', compact('events', 'documentations'));
    }

    public function edit(Documentation $documentation)
    {
        if ($documentation->organizer_id != auth()->id()) {
            abort(403);
        }

        return view('organizer.documentation.edit', compact('documentation'));
    }

    public function update(Request $request, Documentation $documentation)
    {
        if ($documentation->organizer_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'file' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
            'note' => 'nullable|string|max:255'
        ]);

        $data = [
            'note' => $request->note
        ];

        if ($request->hasFile('file')) {
            if (Storage::disk('public')->exists($documentation->file_path)) {
                Storage::disk('public')->delete($documentation->file_path);
            }
            $data['file_path'] = $request->file('file')->store('documentations', 'public');
        }

        $documentation->update($data);

        return redirect()->route('documentation.index', ['event_id' => $documentation->event_id])
            ->with('success', 'Documentation updated successfully.');
    }

    public function destroy(Documentation $documentation)
    {
        if ($documentation->organizer_id != auth()->id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($documentation->file_path)) {
            Storage::disk('public')->delete($documentation->file_path);
        }
        $documentation->delete();

        return back()->with('success', 'Documentation deleted successfully.');
    }

    public function verify($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $documentation = Documentation::findOrFail($id);

        if ($documentation->status !== 'pending') {
            return response()->json([
                'message' => 'Status dokumentasi sudah tidak bisa diubah karena sudah di' . $documentation->status . '.'
            ], 400);
        }

        $documentation->update([
            'status' => $request->status
        ]);

        $event = Event::find($documentation->event_id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        // ✅ Notifikasi dibedakan untuk dokumentasi yang berbeda (berdasarkan feedback)
        $fileName = basename($documentation->file_path);
        $docDetail = $documentation->note ? " (Note: '" . Str::limit($documentation->note, 30) . "')" : " (File: " . $fileName . ")";
        
        Notification::create([
            'user_id' => $event->organizer_id,
            'title' => $request->status === 'approved' 
                ? 'Documentation Approved: ' . Str::limit($event->title, 20) 
                : 'Documentation Rejected: ' . Str::limit($event->title, 20),
            'message' => $request->status === 'approved'
                ? 'Your event documentation for "' . $event->title . '"' . $docDetail . ' has been approved.'
                : 'Your event documentation for "' . $event->title . '"' . $docDetail . ' has been rejected. Please review.',
            'type' => $request->status === 'approved' ? 'success' : 'error',
            'action_url' => '/organizer/documentation/' . $event->id,
            'is_read' => false
        ]);

        // 🔥 AUTOMATIC POINT AWARDING
        if ($request->status === 'approved') {
            // Cek apakah poin sudah pernah dibagikan untuk event ini
            if (!Point::where('event_id', $event->id)->exists()) {
                $registrations = EventRegistration::where('event_id', $event->id)->get();
                $pointsEarned = $event->duration * 10;

                if ($registrations->isNotEmpty()) {
                    $pointsData = [];
                    foreach ($registrations as $registration) {
                        $pointsData[] = [
                            'user_id' => $registration->user_id,
                            'event_id' => $event->id,
                            'points' => $pointsEarned,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    Point::insert($pointsData);
                    User::whereIn('id', $registrations->pluck('user_id'))->increment('points', $pointsEarned);
                }
            }
        }

        return response()->json([
            'message' => 'Verifikasi berhasil + notifikasi terkirim',
            'status' => $documentation->status
        ]);
    }
}