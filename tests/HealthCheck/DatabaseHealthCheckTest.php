<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HealthCheck\DatabaseHealthCheckTest;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Services\DatabaseHealthCheckService;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(DatabaseHealthCheckService::class);

it('shows ok when the database connection succeeds', function () {
    $connection = $this->mock(SqlServerConnection::class);
    $connection->allows('getReadPdo')->andReturnNull();

    $db = $this->mock(DatabaseManager::class);
    $db->allows('connection')->andReturn($connection);

    $service = new DatabaseHealthCheckService($db);

    expect($service->status())
        ->toBeArray()
        ->toHaveKey('status', $service::OK)
        ->toHaveKey('message', 'Database connection is okay')
        ->toHaveKey('context', []);
});

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
        ->toHaveKey('context.exception.error', 'Unsupported driver [__mariadb].')
        ->toHaveKey('context.exception.class')
        ->toHaveKey('context.exception.line')
        ->toHaveKey('context.exception.file');
});

it('builds a problem payload with defaults', function () {
    $service = new DatabaseHealthCheckService(app('db'));

    expect($service->problem())
        ->toBe([
            'status' => $service::PROBLEM,
            'message' => '',
            'context' => [],
        ]);
});
