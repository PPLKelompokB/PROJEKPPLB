<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;

Route::middleware('auth')->group(function () {
    Route::get('/event/{id}/participants', [ParticipantController::class, 'index']);
});
#