<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;

Route::get('/events/search', [EventController::class, 'index']);
