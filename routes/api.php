<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| Event API
|--------------------------------------------------------------------------
*/

Route::prefix('events')->group(function () {
    Route::get('/search', [EventController::class, 'index']);
    Route::get('/{id}/participants', [ParticipantController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Documentation API
|--------------------------------------------------------------------------
*/

Route::prefix('documentations')->group(function () {
    Route::post('/', [DocumentationController::class, 'store']);
    Route::get('/', [DocumentationController::class, 'index']);
    Route::put('/{id}/verify', [DocumentationController::class, 'verify']);
});

/*
|--------------------------------------------------------------------------
| Notification API
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
});