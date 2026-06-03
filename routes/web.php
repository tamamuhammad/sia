<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebHookController;
use Illuminate\Support\Facades\Route;

Route::get('/profile/print-qr/{studentId?}', [ProfileController::class, 'printQr'])
    ->middleware(['auth'])
    ->name('profile.print-qr');

Route::any('/iclock/cdata', [WebHookController::class, 'acceptCData']);

Route::get('/monitor-absen', [WebHookController::class, 'monitor']);

Route::get('/monitor-absen/clear', [WebHookController::class, 'cleanMonitor']);