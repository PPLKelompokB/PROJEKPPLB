<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index($userId)
    {
        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->is_read = true;
        $notif->save();

        return response()->json([
            'message' => 'Notifikasi ditandai sebagai dibaca'
        ]);
    }
}