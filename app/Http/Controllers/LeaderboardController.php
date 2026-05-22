<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index()
    {
        $topVolunteers = User::where('role', 'volunteer')
            ->orderBy('points', 'desc')
            ->take(10)
            ->get();

        $stats = [
            'total_volunteers' => User::where('role', 'volunteer')->count(),
            'total_events' => Event::where('status', 'published')->count(),
        ];

        $userRank = null;
        if (Auth::check() && Auth::user()->role === 'volunteer') {
            $userRank = User::where('role', 'volunteer')
                ->where('points', '>', Auth::user()->points)
                ->count() + 1;
        }

        return view('leaderboard.index', compact('topVolunteers', 'stats', 'userRank'));
    }

    public function full()
    {
        $volunteers = User::where('role', 'volunteer')
            ->orderBy('points', 'desc')
            ->paginate(25);

        return view('leaderboard.full', compact('volunteers'));
    }
}