<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Repositories\CustomerRepositoryTest;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Database\SqlServerConnection;
use Rudashi\Optima\Enums\CustomerGroup;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Tests\TestCase;
use stdClass;

uses(TestCase::class);

mutates(CustomerRepository::class);

function fakeCustomerRow(array $override = []): stdClass
{
    return (object) array_merge([
        'id'              => fake()->numberBetween(1, 9999),
        'code'            => fake()->lexify('????'),
        'company'         => fake()->company(),
        'name_line_two'   => null,
        'name_line_three' => null,
        'country'         => null,
        'city'            => null,
        'postal_code'     => null,
        'street'          => null,
        'building_number' => null,
        'suite_number'    => null,
        'nip'             => null,
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

    $this->repository = new CustomerRepository(new OptimaService($resolver));
});

it('returns a Customer when found by code', function () {
    $row = fakeCustomerRow(['code' => 'TEST']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) =>
            str_contains($sql, 'Knt_Kod') && in_array('TEST', $bindings, true)
        )
        ->andReturn([$row]);

    expect($this->repository->findByCode('TEST'))
        ->toBeInstanceOf(Customer::class)
        ->code->toBe('TEST');
});

it('adds group filter to query when group is provided', function () {
    $row = fakeCustomerRow(['code' => 'SUPPLIER1']);

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) =>
            str_contains($sql, 'Knt_Kod') &&
            str_contains($sql, 'Knt_Grupa') &&
            in_array('SUPPLIER1', $bindings, true) &&
            in_array(CustomerGroup::SUPPLIER->value, $bindings, true)
        )
        ->andReturn([$row]);

    expect($this->repository->findByCode('SUPPLIER1', CustomerGroup::SUPPLIER->value))
        ->toBeInstanceOf(Customer::class);
});

it('throws RecordsNotFoundException when code is not found', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect(fn () => $this->repository->findByCode('MISSING'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => 'MISSING']),
        );
});

it('throws RecordsNotFoundException when code exists but not in given group', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect(fn () => $this->repository->findByCode('SUPPLIER1', CustomerGroup::SUBCONTRACTOR->value))
        ->toThrow(RecordsNotFoundException::class);
});

it('returns a Collection of Customers when found by id', function () {
    $rows = [fakeCustomerRow(['id' => 10]), fakeCustomerRow(['id' => 20])];

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql, $bindings) =>
            str_contains($sql, 'Knt_KntId') &&
            in_array(10, $bindings, true) &&
            in_array(20, $bindings, true)
        )
        ->andReturn($rows);

    expect($this->repository->find(10, 20))
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Customer::class);
});

it('throws RecordsNotFoundException when no customers found by id', function () {
    $this->connection->shouldReceive('select')->once()->andReturn([]);

    expect(fn () => $this->repository->find(99999))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given id is invalid or not in the OPTIMA.'),
        );
});
