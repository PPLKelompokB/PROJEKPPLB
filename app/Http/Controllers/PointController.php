<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Point;

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

        return view('points.index', [
            'points' => $points,
            'totalPoints' => $totalPoints,
        ]);
    }
}