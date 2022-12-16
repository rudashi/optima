<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\OptimaServiceTest;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;
use Rudashi\Optima\Tests\HelperClasses\FakeDTO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

function dto(): FakeDTO
{
    return new FakeDTO(id: fake()->numberBetween(1, 100));
}

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
    expect(optima())
        ->toBeInstanceOf(QueryBuilder::class)
        ->toBeInstanceOf(Builder::class)
    ->and(optima(false))
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
