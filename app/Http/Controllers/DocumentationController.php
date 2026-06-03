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

        $documentation->update([
            'status' => $request->status
        ]);

        $event = Event::find($documentation->event_id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        // ✅ Notifikasi tetap dipakai (sudah bagus)
        Notification::create([
            'user_id' => $event->organizer_id,
            'title' => 'Hasil Verifikasi Dokumentasi',
            'message' => $request->status === 'approved'
                ? 'Dokumentasi kegiatan Anda telah disetujui.'
                : 'Dokumentasi ditolak, silakan upload ulang.',
            'type' => $request->status === 'approved' ? 'success' : 'error',
            'is_read' => false
        ]);

        // 🔥 AUTOMATIC POINT AWARDING
        if ($request->status === 'approved') {
            $registrations = EventRegistration::where('event_id', $event->id)->get();
            $pointsEarned = $event->duration * 10;

            foreach ($registrations as $registration) {
                // Check if point has already been awarded to avoid duplicate
                $existingPoint = Point::where('event_id', $event->id)
                                      ->where('user_id', $registration->user_id)
                                      ->first();
                if (!$existingPoint) {
                    Point::create([
                        'user_id' => $registration->user_id,
                        'event_id' => $event->id,
                        'points' => $pointsEarned
                    ]);

                    $user = User::find($registration->user_id);
                    if ($user) {
                        $user->points += $pointsEarned;
                        $user->save();
                    }
                }
            }
        }

        return response()->json([
            'message' => 'Verifikasi berhasil + notifikasi terkirim',
            'status' => $documentation->status
        ]);
    }
}