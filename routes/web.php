<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\DocumentationController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index']);

/*
|--------------------------------------------------------------------------
| GUEST
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

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

    /*
    |--------------------------------------------------------------------------
    | VOLUNTEER
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:volunteer')->group(function () {
        Route::get('/volunteer/dashboard', [DashboardController::class, 'volunteer'])
            ->name('volunteer.dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | POINTS
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:volunteer')->group(function () {
    Route::get('/points', [App\Http\Controllers\PointController::class, 'index'])
        ->name('points.index');
    });

    /*
    |--------------------------------------------------------------------------
    | ORGANIZER
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:organizer')->group(function () {

        Route::get('/organizer/dashboard', [DashboardController::class, 'organizer'])
            ->name('organizer.dashboard');

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTATION
        |--------------------------------------------------------------------------
        */
        Route::post('/documentation/upload', [DocumentationController::class, 'store'])
            ->name('documentation.store');

        Route::get('/documentation', [DocumentationController::class, 'index'])
            ->name('documentation.index');

        /*
        |--------------------------------------------------------------------------
        | EVENTS (ORGANIZER)
        |--------------------------------------------------------------------------
        */
        Route::prefix('events')->group(function () {

            Route::get('/manage', [EventController::class, 'manage'])
                ->name('events.manage');

            Route::get('/{id}/detail', [EventController::class, 'detail'])
                ->where('id', '[0-9]+')
                ->name('events.detail');

            Route::get('/{id}/edit', [EventController::class, 'edit'])
                ->where('id', '[0-9]+')
                ->name('events.edit');

            Route::get('/{id}/participants', [EventController::class, 'participants'])
                ->where('id', '[0-9]+')
                ->name('events.participants');

            Route::put('/{id}/update', [EventController::class, 'update'])
                ->where('id', '[0-9]+')
                ->name('events.update');
        });

        Route::post('/attendance/{registrationId}/mark',
            [AttendanceController::class, 'mark']
        )->name('attendance.mark');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
            ->name('admin.dashboard');

        Route::post('/documentation/{id}/verify', [DocumentationController::class, 'verify'])
            ->where('id', '[0-9]+')
            ->name('documentation.verify');
    });
});

/*
|--------------------------------------------------------------------------
| PUBLIC EVENT (HARUS DI PALING BAWAH)
|--------------------------------------------------------------------------
*/
Route::get('/events/{id}', [EventController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('events.show');

/*
|--------------------------------------------------------------------------
| UPLOAD DOCUMENTATION PAGE
|--------------------------------------------------------------------------
*/
Route::get('/events/{id}/documentation/upload', function ($id) {
    $event = \App\Models\Event::findOrFail($id);
    return view('organizer.documentation.upload', compact('event'));
})
->where('id', '[0-9]+')
->middleware('auth', 'role:organizer')
->name('documentation.create');