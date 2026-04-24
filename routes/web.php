<?php

use App\Http\Controllers\EventController;

Route::get('/events/{id}', [EventController::class, 'show'])
    ->name('events.show');
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.post');
