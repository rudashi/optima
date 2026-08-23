<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Services\QueryBuilderTest;

use Illuminate\Support\Facades\DB;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\QueryBuilder;
use Rudashi\Optima\Tests\Fixtures\DepartmentEmployeesRelation;
use Rudashi\Optima\Tests\Fixtures\DepartmentManagerRelation;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

beforeEach(fn () => skipUnlessMssql());

mutates(QueryBuilder::class);

it('returns query results as a package Collection from the live schema', function () {
    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->get();

    expect($result)
        ->toBeInstanceOf(Collection::class)
        ->and($result->count())->toBeGreaterThanOrEqual(5);
});

it('compiles and executes the WITH (NOLOCK) table hint against the live schema', function () {
    $queries = [];

    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->noLock()
        ->get();

    expect($result->count())->toBeGreaterThanOrEqual(5)
        ->and(collect($queries)->contains(fn ($sql) => str_contains($sql, 'WITH (NOLOCK)')))->toBeTrue();
});

it('resolves a hasOne relation against the live schema', function () {
    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->whereIn('CNT_CntId', [2, 3, 4, 5])
        ->orderBy('CNT_CntId')
        ->select(['CNT_CntId as id', 'CNT_Nazwa as name'])
        ->hasOne(DepartmentManagerRelation::class, 'id', 'dept_id', 'manager')
        ->get();

    expect($result)->toHaveCount(4);

    $byId = $result->keyBy('id');

    expect($byId->get(2)->manager->manager_id)->toBe(1)
        ->and($byId->get(3)->manager->manager_id)->toBe(2)
        ->and($byId->get(4)->manager->manager_id)->toBe(1)
        ->and($byId->get(5)->manager->manager_id)->toBe(5);
});

it('resolves a hasMany relation against the live schema, defaulting to an empty array', function () {
    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->whereIn('CNT_CntId', [1, 2, 3])
        ->orderBy('CNT_CntId')
        ->select(['CNT_CntId as id'])
        ->hasMany(DepartmentEmployeesRelation::class, 'id', 'dept_id', 'employees')
        ->get();

    $byId = $result->keyBy('id');

    expect($byId->get(1)->employees)->toBe([])
        ->and($byId->get(2)->employees)->toHaveCount(2)
        ->and(array_column($byId->get(2)->employees, 'code'))->toBe(['001E', '002E'])
        ->and($byId->get(3)->employees)->toHaveCount(1)
        ->and($byId->get(3)->employees[0]->code)->toBe('003E');
});

it('does not run relations when there are no owner rows', function () {
    $relation = new DepartmentEmployeesRelation($this->service);
    $this->app->instance(DepartmentEmployeesRelation::class, $relation);

    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->whereIn('CNT_CntId', [9999])
        ->select(['CNT_CntId as id'])
        ->hasMany(DepartmentEmployeesRelation::class, 'id', 'dept_id', 'employees')
        ->get();

    expect($result)->toBeEmpty()
        ->and($relation->calls)->toBe(0);
});

it('loads a relation for a single owner row', function () {
    $relation = new DepartmentManagerRelation($this->service);
    $this->app->instance(DepartmentManagerRelation::class, $relation);

    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->where('CNT_CntId', 5)
        ->select(['CNT_CntId as id'])
        ->hasOne(DepartmentManagerRelation::class, 'id', 'dept_id', 'manager')
        ->get();

    expect($result)->toHaveCount(1)
        ->and($result->first()->manager->manager_id)->toBe(5)
        ->and($relation->calls)->toBe(1);
});

it('resolves a hasMany relation built from a closure against the live schema', function () {
    $result = $this->service->newQuery()
        ->from('CDN.Centra')
        ->whereIn('CNT_CntId', [2, 3])
        ->orderBy('CNT_CntId')
        ->select(['CNT_CntId as id'])
        ->hasMany(
            function (QueryBuilder $query, array $ids) {
                return $query->from('CDN.Pracidx')
                    ->whereIn('PRI_CntId', $ids)
                    ->select(['PRI_CntId as dept_id', 'PRI_Kod as code']);
            },
            'id',
            'dept_id',
            'employees'
        )
        ->get();

    $byId = $result->keyBy('id');

    expect(array_column($byId->get(2)->employees, 'code'))->toBe(['001E', '002E'])
        ->and($byId->get(3)->employees[0]->code)->toBe('003E');
});
