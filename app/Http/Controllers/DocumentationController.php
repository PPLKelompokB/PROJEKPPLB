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
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'note' => 'nullable|string|max:255'
        ]);

        $event = Event::findOrFail($request->event_id);

        // ✅ Authorization (biar tidak sembarang upload)
        if (auth()->id() !== $event->organizer_id) {
            return response()->json([
                'message' => 'Tidak diizinkan upload dokumentasi untuk event ini'
            ], 403);
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

        return response()->json([
            'message' => 'Dokumentasi berhasil diupload',
            'data' => $documentation
        ], 201);
    }

    public function index()
    {
        return response()->json(
            Documentation::with('event')->latest()->get()
        );
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