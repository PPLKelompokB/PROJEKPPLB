<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\AdminDocumentationController;
use App\Http\Controllers\OrganizerDocumentationController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/events/{id}/register', [EventController::class, 'register'])
    ->name('events.register');
    
    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::put('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::put('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::post('/events/{id}/register', [EventController::class, 'register'])->name('events.register');
    
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

        Route::get('/organizer/documentation', [OrganizerDocumentationController::class, 'index'])
            ->name('organizer.documentation.index');

        Route::get('/organizer/documentation/{eventId}', [OrganizerDocumentationController::class, 'show'])
            ->where('eventId', '[0-9]+')
            ->name('organizer.documentation.show');

        Route::post('/organizer/documentation', [OrganizerDocumentationController::class, 'store'])
            ->name('organizer.documentation.store');

        Route::delete('/organizer/documentation/{id}', [OrganizerDocumentationController::class, 'destroy'])
            ->where('id', '[0-9]+')
            ->name('organizer.documentation.destroy');

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

        Route::get('/admin/documentation', [AdminDocumentationController::class, 'index'])
            ->name('admin.documentation.index');

        Route::get('/admin/documentation/{eventId}', [AdminDocumentationController::class, 'show'])
            ->where('eventId', '[0-9]+')
            ->name('admin.documentation.show');

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
