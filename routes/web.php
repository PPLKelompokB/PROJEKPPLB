<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;

Route::get('/', [LandingController::class, 'index']);
Route::get('/events', [EventController::class, 'index']) ->name('events.index');
Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store'])
        ->name('login.post');

    Route::get('/register', [RegisterController::class, 'create'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'store'])
        ->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/events/{id}/register', [EventController::class, 'register'])
    ->name('events.register');
    
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', function () {
        return redirect()->route(match (auth()->user()->role) {
            'volunteer' => 'volunteer.dashboard',
            'organizer' => 'organizer.dashboard',
            'admin' => 'admin.dashboard',
            default => 'login'
        });
    });

    Route::middleware('role:volunteer')->group(function () {
        Route::get('/volunteer/dashboard', [DashboardController::class, 'volunteer'])
            ->name('volunteer.dashboard');
    });

    Route::middleware('role:organizer')->group(function () {

        Route::get('/organizer/dashboard', [DashboardController::class, 'organizer'])
            ->name('organizer.dashboard');

        Route::prefix('events')->group(function () {

            Route::get('/manage', [EventController::class, 'manage'])
                ->name('events.manage');

            Route::get('/{id}/detail', [EventController::class, 'detail'])
                ->name('events.detail');

            Route::get('/{id}/edit', [EventController::class, 'edit'])
                ->name('events.edit');

            Route::put('/{id}/update', [EventController::class, 'update'])
                ->name('events.update');

            Route::get('/{id}/participants', [EventController::class, 'participants'])
                ->name('events.participants');
        });

        Route::post('/attendance/{registrationId}/mark',
            [AttendanceController::class, 'mark']
        )->name('attendance.mark');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
            ->name('admin.dashboard');
    });

});

Route::get('/events/{id}', [EventController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('events.show');

    