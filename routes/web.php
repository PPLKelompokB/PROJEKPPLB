<?php

use App\Http\Controllers\EventController;

Route::get('/events/{id}', [EventController::class, 'show'])
    ->name('events.show');
