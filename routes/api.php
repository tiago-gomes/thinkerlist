<?php

use App\Http\Controllers\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Additional authenticated routes go here
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
