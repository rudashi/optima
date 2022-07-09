<?php

declare(strict_types=1);

namespace Rudashi\Optima;

use Illuminate\Support\ServiceProvider;

class OptimaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '../lang/');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/database.php',
            'database.connections'
        );
    }
}
