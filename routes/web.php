<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

Route::get('/', [LandingController::class, 'index']);

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.post');

Route::post('/logout', [LoginController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
});

Route::middleware('auth')->get('/dashboard', function () {
    $role = auth()->user()->role;

    return redirect()->route(match ($role) {
        'volunteer' => 'volunteer.dashboard',
        'organizer' => 'organizer.dashboard',
        'admin' => 'admin.dashboard',
    });
});

Route::middleware(['auth'])->group(function () {

    Route::middleware('role:volunteer')->group(function () {
        Route::get('/volunteer/dashboard', [DashboardController::class, 'volunteer'])
            ->name('volunteer.dashboard');
    });

    Route::middleware('role:organizer')->group(function () {
        Route::get('/organizer/dashboard', [DashboardController::class, 'organizer'])
            ->name('organizer.dashboard');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
            ->name('admin.dashboard');
    });

});