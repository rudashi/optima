<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\OptimaServiceTest;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

test('can load database configuration', function () {

    expect(app('db')->connection(OptimaService::$connection))
        ->toBeInstanceOf(SqlServerConnection::class);
});

it('get custom Query builder', function () {

    expect($this->db->newQuery())
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class);
});

it('can use helper function `optima`', function () {

    expect(optima())
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class);

    expect(optima(false))
        ->toBeInstanceOf(OptimaService::class)
        ->newQuery()
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class);
});

it('can parse multiple ids to flatten array', function () {

    $ids = [1, 'test', '34', 0];

    expect($this->db->parseIds(1, 'test', '34', 0))
        ->toBeArray()
        ->toHaveCount(4)
        ->toBe($ids);

    expect($this->db->parseIds($ids))
        ->toBeArray()
        ->toHaveCount(4)
        ->toBe($ids);
});
