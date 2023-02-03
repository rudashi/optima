<?php

use Illuminate\Support\Facades\Route;
use Rudashi\Optima\Controllers\HealthCheckController;

Route::prefix('api')->middleware('api')->group(static function () {
    Route::get('optima/ping', HealthCheckController::class)->name('api.optima.ping');
});
