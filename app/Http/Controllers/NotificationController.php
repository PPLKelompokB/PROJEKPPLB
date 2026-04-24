<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request, $userId)
    {
        $query = Notification::where('user_id', auth()->id())->latest();

        if ($request->has('is_read')) {
            $query->where('is_read', $request->is_read);
        }

        return response()->json(
            $query->paginate(10)
        );
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $notif->update(['is_read' => true]);

        return response()->json([
            'message' => 'Notifikasi ditandai sebagai dibaca'
        ]);
    }
}