<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HealthCheck\DatabaseHealthCheckTest;

use Rudashi\Optima\Services\DatabaseHealthCheckService;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('shows problem when database driver is incorrect', function () {
    config(['database.connections.optima.driver' => 'sqlite']);
    $service = new DatabaseHealthCheckService(app('db'));

    expect($service->status())
        ->toBeArray()
        ->toHaveKeys([
            'status',
            'message',
            'context',
        ])
        ->toHaveKey('status', $service::PROBLEM)
        ->toHaveKey('message', 'Could not connect to db')
        ->toHaveKey('context.connection', 'optima');
});

it('shows problem when driver is not installed on server', function () {
    config(['database.connections.optima.driver' => '__mariadb']);
    $service = new DatabaseHealthCheckService(app('db'));

    expect($service->status())
        ->toBeArray()
        ->toHaveKeys([
            'status',
            'message',
            'context',
        ])
        ->toHaveKey('status', $service::PROBLEM)
        ->toHaveKey('message', 'Could not connect to db')
        ->toHaveKey('context.connection', 'optima')
        ->toHaveKey('context.exception.error', 'Unsupported driver [__mariadb].');
});
