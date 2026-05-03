<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
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