<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'cors'])->group(function () {
    Route::post('/imports/upload', [App\Http\Controllers\Api\ImportController::class, 'upload']);
    Route::get('/imports/{import}/status', [App\Http\Controllers\Api\ImportController::class, 'status']);
    Route::get('/imports/{import}/summary', [App\Http\Controllers\Api\ImportController::class, 'summary']);

    Route::get('/contacts', [App\Http\Controllers\Api\ContactController::class, 'index']);
});
