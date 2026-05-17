<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Repositories\DepartmentRepositoryTest;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\Repositories\DepartmentRepository;
use Rudashi\Optima\Tests\TestCase;
use stdClass;

uses(TestCase::class);

mutates(DepartmentRepository::class);

function fakeDepartmentRow(array $override = []): stdClass
{
    return (object) array_merge([
        'id'        => fake()->numberBetween(1, 999),
        'name'      => strtoupper(fake()->word()),
        'user_code' => fake()->lexify('???'),
        'parent_id' => fake()->numberBetween(1, 20),
    ], $override);
}

beforeEach(function () {
    $real = app('db')->connection('optima');

    $this->connection = $this->mock(SqlServerConnection::class);
    $this->connection->allows('getQueryGrammar')->andReturn($real->getQueryGrammar());
    $this->connection->allows('getPostProcessor')->andReturn($real->getPostProcessor());

    $resolver = $this->mock(DatabaseManager::class);
    $resolver->allows('connection')->andReturn($this->connection);

    $this->repository = new DepartmentRepository(new OptimaService($resolver));
});

it('returns a Collection of Departments for all()', function () {
    $rows = [fakeDepartmentRow(), fakeDepartmentRow(), fakeDepartmentRow()];

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) => str_contains($sql, '[CDN].[Centra]'))
        ->andReturn($rows);

    expect($this->repository->all())
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(3)
        ->each->toBeInstanceOf(Department::class);
});

it('returns a Department when found by code', function () {
    $row = fakeDepartmentRow(['name' => 'DRUK']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) =>
            str_contains($sql, 'CNT_Kod') && in_array('DRUK', $bindings, true)
        )
        ->andReturn([$row]);

    expect($this->repository->findByCode('DRUK'))
        ->toBeInstanceOf(Department::class)
        ->name->toBe('DRUK');
});

it('uppercases the code before querying', function () {
    $row = fakeDepartmentRow(['name' => 'DRUK']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) => in_array('DRUK', $bindings, true))
        ->andReturn([$row]);

    expect($this->repository->findByCode('druk'))
        ->toBeInstanceOf(Department::class);
});

it('find() is an alias for findByCode()', function () {
    $row = fakeDepartmentRow(['name' => 'BIURO']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) => in_array('BIURO', $bindings, true))
        ->andReturn([$row]);

    expect($this->repository->find('BIURO'))
        ->toBeInstanceOf(Department::class)
        ->name->toBe('BIURO');
});

it('throws RecordsNotFoundException when code is not found', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect(fn () => $this->repository->findByCode('MISSING'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => 'MISSING']),
        );
});

it('throws an exception when department is archived', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect(fn () => $this->repository->findByCode('ARCHIVED'))
        ->toThrow(RecordsNotFoundException::class);
});
