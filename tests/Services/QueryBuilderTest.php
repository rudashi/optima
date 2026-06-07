<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Services\QueryBuilderTest;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;
use Rudashi\Optima\Tests\Fixtures\FakeRelation;
use Rudashi\Optima\Tests\TestCase;
use stdClass;

uses(TestCase::class);

mutates(QueryBuilder::class);

beforeEach(function () {
    $real = app('db')->connection('optima');

    $this->connection = $this->mock(SqlServerConnection::class);
    $this->connection->allows('getQueryGrammar')->andReturn($real->getQueryGrammar());
    $this->connection->allows('getPostProcessor')->andReturn($real->getPostProcessor());

    $resolver = $this->mock(DatabaseManager::class);
    $resolver->allows('connection')->andReturn($this->connection);

    $this->builder = (new OptimaService($resolver))->newQuery();
});

it('returns results as a package Collection', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([(object) ['id' => 1], (object) ['id' => 2]]);

    expect($this->builder->from('CDN.Kontrahenci')->get())
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(stdClass::class);
});

it('selects all columns by default', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) => str_starts_with($sql, 'select * from'))
        ->andReturn([]);

    $this->builder->from('CDN.Kontrahenci')->get();
});

it('wraps a single column into the select list', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) => str_contains($sql, '[Knt_Kod]'))
        ->andReturn([]);

    $this->builder->from('CDN.Kontrahenci')->get('Knt_Kod');
});

it('returns the first row as stdClass', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([(object) ['id' => 7]]);

    expect($this->builder->from('CDN.Kontrahenci')->first())
        ->toBeInstanceOf(stdClass::class)
        ->id->toBe(7);
});

it('returns null when first finds no rows', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect($this->builder->from('CDN.Kontrahenci')->first())->toBeNull();
});

it('maps each row through the callback with getTo', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([(object) ['id' => 1], (object) ['id' => 2]]);

    $result = $this->builder->from('CDN.Kontrahenci')->getTo(fn ($item) => $item->id * 10);

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result->all())->toBe([10, 20]);
});

it('compiles the WITH (NOLOCK) table hint', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) => str_contains($sql, '[CDN].[Kontrahenci] WITH (NOLOCK)'))
        ->andReturn([]);

    $this->builder->from('CDN.Kontrahenci')->noLock()->get();
});

it('returns itself when registering relations', function () {
    $this->app->instance(FakeRelation::class, new FakeRelation());

    expect($this->builder->hasOne(FakeRelation::class, 'id', 'customer_id', 'address'))
        ->toBe($this->builder)
        ->and($this->builder->hasMany(FakeRelation::class, 'id', 'order_id', 'items'))
        ->toBe($this->builder)
        ->and($this->builder->hasManyThrough(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments'))
        ->toBe($this->builder);
});

it('matches a single related model with hasOne', function () {
    $relation = new FakeRelation([
        (object) ['customer_id' => 1, 'city' => 'GDAŃSK'],
    ]);
    $this->app->instance(FakeRelation::class, $relation);

    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([(object) ['id' => 1], (object) ['id' => 2]]);

    $result = $this->builder->from('CDN.Kontrahenci')
        ->hasOne(FakeRelation::class, 'id', 'customer_id', 'address')
        ->get();

    expect($relation->receivedKeys)->toBe([1, 2])
        ->and($result->first()->address)->toBeInstanceOf(stdClass::class)
        ->and($result->first()->address->city)->toBe('GDAŃSK')
        ->and($result->last()->address)->toBeNull();
});

it('groups related models per owner with hasMany', function () {
    $relation = new FakeRelation([
        (object) ['order_id' => 1, 'name' => 'A'],
        (object) ['order_id' => 1, 'name' => 'B'],
    ]);
    $this->app->instance(FakeRelation::class, $relation);

    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([(object) ['id' => 1], (object) ['id' => 2]]);

    $result = $this->builder->from('CDN.Zamowienia')
        ->hasMany(FakeRelation::class, 'id', 'order_id', 'items')
        ->get();

    expect($result->first()->items)->toHaveCount(2)
        ->and($result->last()->items)->toBe([]);
});

it('builds the relation query from a closure with hasMany', function () {
    $received = [];

    $this->connection->shouldReceive('select')
        ->twice()
        ->andReturn(
            [(object) ['id' => 1], (object) ['id' => 2]],
            [(object) ['parent_id' => 2, 'name' => 'A']],
        );

    $result = $this->builder->from('CDN.Zamowienia')
        ->hasMany(
            function (QueryBuilder $query, array $ids) use (&$received) {
                $received = $ids;

                return $query->from('CDN.Pozycje')->whereIn('parent_id', $ids);
            },
            'id',
            'parent_id',
            'items'
        )
        ->get();

    expect($received)->toBe([1, 2])
        ->and($result->first()->items)->toBe([])
        ->and($result->last()->items)->toHaveCount(1);
});

it('matches related models through a pivot with hasManyThrough', function () {
    $relation = new FakeRelation([
        (object) ['dept_id' => 100, 'name' => 'IT'],
        (object) ['dept_id' => 200, 'name' => 'HR'],
    ]);
    $this->app->instance(FakeRelation::class, $relation);

    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([
            (object) [
                'id'        => 1,
                'contracts' => [
                    (object) ['dept_id' => 100, 'employee_id' => 1],
                    (object) ['dept_id' => 200, 'employee_id' => 1],
                ],
            ],
            (object) [
                'id'        => 2,
                'contracts' => [
                    (object) ['dept_id' => 100, 'employee_id' => 2],
                ],
            ],
        ]);

    $result = $this->builder->from('CDN.Pracidx')
        ->hasManyThrough(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments')
        ->get();

    expect($relation->receivedKeys)->toBe([100, 200, 100])
        ->and($result->first()->departments)->toHaveCount(2)
        ->and($result->last()->departments)->toHaveCount(1)
        ->and($result->last()->departments[0]->name)->toBe('IT');
});

it('loads relations for a single result', function () {
    $relation = new FakeRelation([
        (object) ['order_id' => 1, 'name' => 'A'],
    ]);
    $this->app->instance(FakeRelation::class, $relation);

    $this->connection->shouldReceive('select')
        ->once()
        ->andReturn([(object) ['id' => 1]]);

    $result = $this->builder->from('CDN.Zamowienia')
        ->hasMany(FakeRelation::class, 'id', 'order_id', 'items')
        ->get();

    expect($result->first()->items)->toHaveCount(1);
});

it('does not run relations when there are no results', function () {
    $relation = new FakeRelation();
    $this->app->instance(FakeRelation::class, $relation);

    $this->connection->shouldReceive('select')->once()->andReturn([]);

    $result = $this->builder->from('CDN.Zamowienia')
        ->hasMany(FakeRelation::class, 'id', 'order_id', 'items')
        ->get();

    expect($result)->toBeEmpty()
        ->and($relation->calls)->toBe(0);
});
