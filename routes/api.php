<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Rudashi\Optima\Controllers\HealthCheckController;

Route::prefix('api')->middleware('api')->group(static function (): void {
    Route::get('optima/ping', HealthCheckController::class)->name('api.optima.ping');
});
