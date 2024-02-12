<?php

declare(strict_types=1);

use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;

if (! function_exists('optima')) {
    function optima(bool $query = true): QueryBuilder|OptimaService
    {
        $service = app(OptimaService::class);

        if ($query) {
            return $service->newQuery();
        }

        return $service;
    }
}
