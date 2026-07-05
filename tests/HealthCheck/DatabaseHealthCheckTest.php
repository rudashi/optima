<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HealthCheck\DatabaseHealthCheckTest;

use Exception;
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
        ->toBe([
            'status' => $service::OK,
            'message' => 'Database connection is okay',
            'context' => [],
        ]);
});

it('shows problem when the database connection fails', function () {
    $connection = $this->mock(SqlServerConnection::class);
    $connection->allows('getReadPdo')->andThrow(new Exception('Connection refused'));

    $db = $this->mock(DatabaseManager::class);
    $db->allows('connection')->andReturn($connection);

    $service = new DatabaseHealthCheckService($db);

    expect($service->status())
        ->toBeArray()
        ->toHaveKey('status', $service::PROBLEM)
        ->toHaveKey('message', 'Could not connect to db')
        ->toHaveKey('context.connection', 'optima')
        ->toHaveKey('context.exception.error', 'Connection refused')
        ->toHaveKey('context.exception.class', Exception::class)
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
