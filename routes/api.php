<?php

use App\Http\Controllers\PartController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/episodes/{episodeId}/parts', [PartController::class, 'getAllParts'])
    ->name('parts.all');

Route::post('/episodes/parts', [PartController::class, 'create'])
    ->name('parts.create');

Route::delete('/episodes/parts', [PartController::class, 'delete'])
    ->name('parts.delete');
