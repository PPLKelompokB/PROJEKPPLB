<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Point;
use App\Models\User;
use App\Models\EventRegistration;

class PointController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $points = Point::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $totalPoints = $user->points ?? 0;
        
        $totalEvents = EventRegistration::where('user_id', $user->id)->count();

        $rank = User::where('role', 'volunteer')
            ->where('points', '>', $totalPoints)
            ->count() + 1;

        $leaderboard = User::where('role', 'volunteer')
            ->orderBy('points', 'desc')
            ->take(10)
            ->get();

        return view('points.index', compact('points', 'totalPoints', 'totalEvents', 'rank', 'leaderboard', 'user'));
    }
}