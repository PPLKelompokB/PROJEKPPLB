<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Event API
|--------------------------------------------------------------------------
*/

Route::prefix('events')->group(function () {
    Route::get('/search', [EventController::class, 'index']);
    Route::get('/{id}/participants', [ParticipantController::class, 'index']);

    Route::middleware('auth')->group(function () {
        Route::get('/{id}', [EventController::class, 'show']);
    });
});

/*
|--------------------------------------------------------------------------
| Documentation API
|--------------------------------------------------------------------------
*/

Route::prefix('documentations')->middleware('auth')->group(function () {
    Route::post('/', [DocumentationController::class, 'store']);
    Route::get('/', [DocumentationController::class, 'index']);
    Route::put('/{id}/verify', [DocumentationController::class, 'verify']);
});

/*
|--------------------------------------------------------------------------
| Notification API
|--------------------------------------------------------------------------
*/
// Moved to web.php for session auth