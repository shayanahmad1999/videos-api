<?php

use App\Http\Controllers\AuthenticatedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');
Route::get('/auth', [AuthenticatedController::class, 'index'])->name('auth.index');

Route::post('/auth/register', [AuthenticatedController::class, 'register'])->name('auth.register');
Route::post('/auth/login',    [AuthenticatedController::class, 'login'])->name('auth.login');
Route::post('/auth/logout',   [AuthenticatedController::class, 'logout'])->name('auth.logout');

Route::get('/dashboard', [AuthenticatedController::class, 'dashboard'])
    ->middleware('api.auth')
    ->name('dashboard');

Route::get('/videos', function () {
    return view('videos.index');
})->name('videos');

Route::get('/create', function () {
    return view('videos.videos');
});
