<?php

use App\Http\Controllers\PartController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/episodes/{episodeId}/parts', [PartController::class, 'getAllParts'])
    ->name('parts.all');
