<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // 🔥 OPTIONAL: redirect berdasarkan role
            $user = Auth::user();

            if ($user->role === 'admin') {
                return redirect('/admin');
            } elseif ($user->role === 'organizer') {
                return redirect('/organizer');
            }

            return redirect('/'); // volunteer
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }
}