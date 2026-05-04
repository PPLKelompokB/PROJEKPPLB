<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        if (! Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Password salah.']);
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('user', [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ]);

        return match ($user->role) {
            'admin' => redirect()->route('dashboard.admin')->with('success', 'Login berhasil!'),
            'organizer' => redirect()->route('dashboard.organizer')->with('success', 'Login berhasil!'),
            'volunteer' => redirect()->route('dashboard.volunteer')->with('success', 'Login berhasil!'),
            default => redirect()->route('dashboard')->with('success', 'Login berhasil!'),
        };
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah logout');
    }
}
