<?php

use App\Http\Controllers\Api\AuthenticatedSessionController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ---------- user api code (route) in one pattern ---------- //
Route::controller(RegisterController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [RegisterController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// ---------- user api code (route) in other pattern ---------- //
Route::post('/user-register', [RegisteredUserController::class, 'store']);
Route::post('/user-login', [AuthenticatedSessionController::class, 'store']);

Route::get('/user-user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/user-logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');

