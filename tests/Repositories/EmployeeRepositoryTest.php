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
