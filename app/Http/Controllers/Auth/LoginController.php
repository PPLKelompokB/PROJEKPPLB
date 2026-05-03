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

        if (!Auth::attempt($credentials, $request->remember)) {
            return back()->withErrors([
                'email' => 'Email atau password salah',
            ])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->route($this->redirectTo($user->role));
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); 
        $request->session()->regenerateToken(); 

        return redirect('/'); 
    }
    private function redirectTo($role)
    {
        return match ($role) {
            'volunteer' => 'volunteer.dashboard',
            'organizer' => 'organizer.dashboard',
            'admin' => 'admin.dashboard',
            default => 'login'
        };
    }
}