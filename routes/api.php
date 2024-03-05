<?php

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Schedule\ScheduleRuleController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {

    // schedule rules
    Route::get('/schedule-rules', [ScheduleRuleController::class, 'index'])->name('schedule.rules.index');
    Route::post('/schedule-rules', [ScheduleRuleController::class, 'store'])->name('schedule.rules.store');
    Route::patch('/schedule-rules/{id}', [ScheduleRuleController::class, 'update'])->name('schedule.rules.update');

    // authentication
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
