<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;

Route::get('/events/search', [EventController::class, 'index']);
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\NotificationController;

Route::get('/event/{id}/participants', [ParticipantController::class, 'index']);

Route::post('/documentations', [DocumentationController::class, 'store']);

Route::get('/documentations', [DocumentationController::class, 'index']);

Route::put('/documentations/{id}/verify', [DocumentationController::class, 'verify']);

Route::get('/notifications/{userId}', [NotificationController::class, 'index']);

Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
