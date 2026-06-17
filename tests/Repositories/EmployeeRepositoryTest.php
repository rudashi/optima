<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Repositories\EmployeeRepositoryTest;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(EmployeeRepository::class);

beforeEach(function () {
    $real = app('db')->connection('optima');

    $this->connection = $this->mock(SqlServerConnection::class);
    $this->connection->allows('getQueryGrammar')->andReturn($real->getQueryGrammar());
    $this->connection->allows('getPostProcessor')->andReturn($real->getPostProcessor());

    $resolver = $this->mock(DatabaseManager::class);
    $resolver->allows('connection')->andReturn($this->connection);

    $this->repository = new EmployeeRepository(new OptimaService($resolver));
});

it('returns an Employee when found by code', function () {
    $row = fakeEmployeeRow(['code' => '023E']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(
            fn ($sql, $bindings) =>
            str_contains($sql, 'PRI_Kod') && in_array('023E', $bindings, true)
        )
        ->andReturn([$row]);

    expect($this->repository->findByCode('023E'))
        ->toBeInstanceOf(Employee::class)
        ->code->toBe('023E');
});

it('find() is an alias for findByCode()', function () {
    $row = fakeEmployeeRow(['code' => '023E']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) => in_array('023E', $bindings, true))
        ->andReturn([$row]);

    expect($this->repository->find('023E'))
        ->toBeInstanceOf(Employee::class)
        ->code->toBe('023E');
});

it('throws RecordsNotFoundException when employee code is not found', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect(fn () => $this->repository->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given acronym :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('throws RecordsNotFoundException when employee is archived', function () {
    $row = fakeEmployeeRow(['code' => 'XXX', 'deleted' => 1]);

    $this->connection->shouldReceive('select')->once()->andReturn([$row]);

    expect(fn () => $this->repository->findByCode('XXX'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Employee with given acronym :code is archived.', ['code' => 'XXX']),
        );
});

it('has correct constant values', function (int $constant, int $expected) {
    expect($constant)->toBe($expected);
})->with([
    'EMPLOYEE'           => [EmployeeRepository::EMPLOYEE, 1],
    'OWNER'              => [EmployeeRepository::OWNER, 2],
    'EMPLOYEE_FULL_TIME' => [EmployeeRepository::EMPLOYEE_FULL_TIME, 10],
    'CONTRACTOR'         => [EmployeeRepository::CONTRACTOR, 20],
]);

it('selects all required employee columns in SQL', function () {
    $row = fakeEmployeeRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(
            fn ($sql) =>
            str_contains($sql, 'PRI_PraId] as [id]') &&
            str_contains($sql, 'PRI_Kod] as [code]') &&
            str_contains($sql, 'PRI_Imie1') &&
            str_contains($sql, 'PRI_Nazwisko') &&
            str_contains($sql, 'PRI_Archiwalny') &&
            str_contains($sql, 'PRI_CntId] as [department_id]') &&
            str_contains($sql, 'CNT_Kod') &&
            str_contains($sql, 'PRE_HDKEmail') &&
            str_contains($sql, 'DKM_Nazwa') &&
            str_contains($sql, 'PKR_Numer') &&
            str_contains($sql, 'as company')
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});

it('filters by EMPLOYEE and OWNER types in SQL', function () {
    $row = fakeEmployeeRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(
            fn ($sql, $bindings) =>
            str_contains($sql, 'PRI_Typ') &&
            in_array(1, $bindings, true) &&
            in_array(2, $bindings, true)
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});

it('joins with the latest work record in SQL', function () {
    $row = fakeEmployeeRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(
            fn ($sql) =>
            str_contains($sql, 'PracEtaty') &&
            str_contains($sql, 'MAX(PRE_PreId)')
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});

it('joins with RCP card filtered by date in SQL', function () {
    $row = fakeEmployeeRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(
            fn ($sql) =>
            str_contains($sql, 'PracKartyRcp') &&
            str_contains($sql, 'PKR_OkresDo')
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});
