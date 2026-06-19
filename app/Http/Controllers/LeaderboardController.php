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
            ->withCount('attendances')
            ->withMax('attendances', 'created_at')
            ->orderBy('points', 'desc')
            ->take(5)
            ->get();
        $totalVolunteers = User::where('role', 'volunteer')->count();

        $stats = [
            'total_volunteers' => $totalVolunteers,
            'total_events' => Event::where('status', 'published')->count(),
        ];

        $userRank = null;
        $topPercentage = null;

        if (Auth::check() && Auth::user()->role === 'volunteer') {
            $userRank = User::where('role', 'volunteer')
                ->where('points', '>', Auth::user()->points)
                ->count() + 1;
            if ($totalVolunteers > 0) {
                $topPercentage = ceil(($userRank / $totalVolunteers) * 100);
            }
        }

        return view('leaderboard.index', compact('topVolunteers', 'stats', 'userRank', 'topPercentage'));
    }

    public function full(Request $request)
    {
        $sort = $request->input('sort', 'desc');
        $query = User::where('role', 'volunteer')
            ->withCount('attendances');

        if ($sort === 'asc') {
            $query->orderBy('points', 'asc'); 
        } else {
            $query->orderBy('points', 'desc');
        }

        $volunteers = $query->paginate(25)->withQueryString(); 

        return view('leaderboard.full', compact('volunteers'));
    }
}