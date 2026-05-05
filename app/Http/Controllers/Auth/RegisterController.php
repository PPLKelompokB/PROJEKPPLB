<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register', [
            'events' => \App\Models\Event::latest()->take(6)->get(),
            'totalEvents' => \App\Models\Event::count(),
            'totalVolunteers' => \App\Models\User::where('role', 'volunteer')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'role' => 'required|in:admin,organizer,volunteer'
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        Auth::login($user);

        return redirect()->route(match ($user->role) {
            'volunteer' => 'volunteer.dashboard',
            'organizer' => 'organizer.dashboard',
            'admin' => 'admin.dashboard',
        });
    }
}