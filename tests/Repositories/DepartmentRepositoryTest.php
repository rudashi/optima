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

uses(TestCase::class);

mutates(DepartmentRepository::class);

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

it('selects all required department columns in SQL', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) =>
            str_contains($sql, 'CNT_CntId] as [id]') &&
            str_contains($sql, 'CNT_Nazwa] as [name]') &&
            str_contains($sql, 'CNT_ParentId') &&
            str_contains($sql, 'PRI_Kod')
        )
        ->andReturn([]);

    $this->repository->all();
});

it('applies all WHERE filters in query', function () {
    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) =>
            str_contains($sql, 'CNT_Nazwa') &&
            str_contains($sql, 'CNT_Nieaktywny') &&
            str_contains($sql, 'PRI_Typ') &&
            str_contains($sql, 'PRI_Archiwalny') &&
            str_contains($sql, 'CNK_Rodzaj') &&
            in_array('', $bindings, true) &&
            in_array(1, $bindings, true) &&
            in_array(2, $bindings, true) &&
            count(array_filter($bindings, fn ($b) => $b === 0)) === 3
        )
        ->andReturn([]);

    $this->repository->all();
});

it('maps all fields from the database row', function () {
    $row = fakeDepartmentRow([
        'id'        => 7,
        'name'      => 'FINANSE',
        'user_code' => 'FIN',
        'parent_id' => 3,
    ]);

    $this->connection->shouldReceive('select')->once()->andReturn([$row]);

    expect($this->repository->findByCode('FINANSE'))
        ->id->toBe(7)
        ->name->toBe('FINANSE')
        ->user_code->toBe('FIN')
        ->parent_id->toBe(3);
});
