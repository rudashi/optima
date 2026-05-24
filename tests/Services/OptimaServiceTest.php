<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Services\OptimaServiceTest;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\SqlServerConnection;
use PDOException;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(OptimaService::class);

beforeEach(function () {
    $this->service = app(OptimaService::class);
});

test('can load database configuration', function () {
    expect(app('db')->connection(OptimaService::$connection))
        ->toBeInstanceOf(SqlServerConnection::class);
});

it('get custom Query builder', function () {
    expect($this->service->newQuery())
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class);
});

it('can use helper function `optima`', function () {
    $builder = optima();

    expect($builder)
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class);

    $service = optima(false);

    expect($service)
        ->toBeInstanceOf(OptimaService::class)
        ->newQuery()
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class);
});

it('can parse multiple ids to flatten array', function () {
    $ids = [1, 'test', '34', 0];

    expect($this->service->parseIds(1, 'test', '34', 0))
        ->toBeArray()
        ->toHaveCount(4)
        ->toBe($ids);
});

it('can parse collection of models to array of model keys', function () {
    $first = dto();
    $second = dto();
    $third = dto();
    $collection = (new Collection([$first, $second, $third]));

    expect($this->service->parseIds($collection))
        ->toBeArray()
        ->toHaveCount(3)
        ->toMatchArray([$first->id, $second->id, $third->id]);
});

it('uses provided connection name over default', function () {
    $resolver = $this->mock(DatabaseManager::class);
    $service = new OptimaService($resolver, 'custom_connection');

    expect($service->getConnectionName())->toBe('custom_connection');
});

it('returns true when connection is reachable', function () {
    $connection = $this->mock(SqlServerConnection::class);
    $connection->shouldReceive('getReadPdo')->once()->andReturn(true);

    $resolver = $this->mock(DatabaseManager::class);
    $resolver->allows('connection')->andReturn($connection);

    expect((new OptimaService($resolver))->hasConnection())->toBeTrue();
});

it('returns false when connection throws PDOException', function () {
    $connection = $this->mock(SqlServerConnection::class);
    $connection->shouldReceive('getReadPdo')->once()->andThrow(new PDOException('Connection failed'));

    $resolver = $this->mock(DatabaseManager::class);
    $resolver->allows('connection')->andReturn($connection);

    expect((new OptimaService($resolver))->hasConnection())->toBeFalse();
});

it('can switch connection', function () {
    config([
        'database.connections.optima_second' => [
            'driver' => 'sqlite',
            'host' => '192.168.0.0',
            'port' => '3307',
            'database' => 'database',
            'username' => 'user',
            'password' => 'pass',
        ],
    ]);

    expect($this->service->getConnection())
        ->getDriverName()->toBe('sqlsrv');

    $this->service->setConnectionName('optima_second');

    expect($this->service->getConnection())
        ->getDriverName()->toBe('sqlite');
});
