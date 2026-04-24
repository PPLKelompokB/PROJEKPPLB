<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentation;
use App\Models\Event;
use App\Models\Notification;

class DocumentationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $event = Event::findOrFail($request->event_id);

        $documentation = Documentation::create([
            'event_id' => $event->id,
            'organizer_id' => $event->organizer_id, 
            'file_path' => 'dummy.jpg',
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Dokumentasi berhasil diupload (dummy)',
            'data' => $documentation
        ], 201);
    }

    public function index()
    {
        return response()->json(
            Documentation::with('event')->get()
        );
    }

    public function verify($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $documentation = Documentation::findOrFail($id);

        $documentation->status = $request->status;
        $documentation->save();

        $event = Event::find($documentation->event_id);

        if (!$event) {
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
        }

        Notification::create([
            'user_id' => $event->organizer_id,
            'title' => 'Hasil Verifikasi Dokumentasi',
            'message' => $request->status === 'approved'
                ? 'Dokumentasi kegiatan Anda telah disetujui.'
                : 'Dokumentasi ditolak, silakan upload ulang.',
            'type' => $request->status === 'approved' ? 'success' : 'error',
            'is_read' => false
        ]);

        return response()->json([
            'message' => 'Verifikasi berhasil + notifikasi terkirim',
            'status' => $documentation->status
        ]);
    }
}