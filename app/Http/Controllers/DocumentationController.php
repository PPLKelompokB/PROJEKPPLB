<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentation;
use App\Models\Event;
use App\Models\Notification;
use Illuminate\Support\Facades\Storage;

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

        return response()->json([
            'message' => 'Verifikasi berhasil + notifikasi terkirim',
            'status' => $documentation->status
        ]);
    }
}