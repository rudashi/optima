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
use stdClass;

uses(TestCase::class);

mutates(EmployeeRepository::class);

function fakeEmployeeRow(array $override = []): stdClass
{
    return (object) array_merge([
        'id'              => fake()->numberBetween(1, 9999),
        'code'            => fake()->lexify('???E'),
        'firstname'       => fake()->firstName(),
        'lastname'        => fake()->lastName(),
        'email'           => fake()->email(),
        'job_title'       => fake()->jobTitle(),
        'department_id'   => fake()->numberBetween(1, 50),
        'department_name' => strtoupper(fake()->word()),
        'company'         => fake()->company(),
        'rcp'             => null,
        'deleted'         => 0,
    ], $override);
}

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
        ->withArgs(fn ($sql, $bindings) =>
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

it('has correct constant values', function () {
    expect(EmployeeRepository::EMPLOYEE)->toBe(1)
        ->and(EmployeeRepository::OWNER)->toBe(2)
        ->and(EmployeeRepository::EMPLOYEE_FULL_TIME)->toBe(10)
        ->and(EmployeeRepository::CONTRACTOR)->toBe(20);
});

it('maps all fields from the database row', function () {
    $row = fakeEmployeeRow([
        'id'              => 42,
        'code'            => '001E',
        'firstname'       => 'Jan',
        'lastname'        => 'Kowalski',
        'email'           => 'jan@example.com',
        'job_title'       => 'Developer',
        'department_id'   => 5,
        'department_name' => 'IT',
        'company'         => 'Totem',
        'rcp'             => '99999',
        'deleted'         => 0,
    ]);

    $this->connection->shouldReceive('select')->once()->andReturn([$row]);

    expect($this->repository->findByCode('001E'))
        ->id->toBe(42)
        ->code->toBe('001E')
        ->firstname->toBe('Jan')
        ->lastname->toBe('Kowalski')
        ->email->toBe('jan@example.com')
        ->job_title->toBe('Developer')
        ->department_id->toBe(5)
        ->department_name->toBe('IT')
        ->company->toBe('Totem')
        ->rcp->toBe('99999')
        ->deleted->toBeFalse();
});

it('selects all required employee columns in SQL', function () {
    $row = fakeEmployeeRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) =>
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
        ->withArgs(fn ($sql, $bindings) =>
            str_contains($sql, 'PRI_Typ') &&
            in_array(EmployeeRepository::EMPLOYEE, $bindings, true) &&
            in_array(EmployeeRepository::OWNER, $bindings, true)
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});

it('joins with the latest work record in SQL', function () {
    $row = fakeEmployeeRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) =>
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
        ->withArgs(fn ($sql) =>
            str_contains($sql, 'PracKartyRcp') &&
            str_contains($sql, 'PKR_OkresDo')
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});
