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

uses(TestCase::class);

mutates(CustomerRepository::class);

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

it('selects all required customer columns in SQL', function () {
    $row = fakeCustomerRow();

    $this->connection->shouldReceive('select')
        ->once()
        ->withArgs(fn ($sql) =>
            str_contains($sql, 'Knt_KntId') &&
            str_contains($sql, 'Knt_Kod] as [code]') &&
            str_contains($sql, 'Knt_Nazwa1') &&
            str_contains($sql, 'Knt_Nazwa2') &&
            str_contains($sql, 'Knt_Nazwa3') &&
            str_contains($sql, 'Knt_Kraj') &&
            str_contains($sql, 'Knt_Miasto') &&
            str_contains($sql, 'Knt_KodPocztowy') &&
            str_contains($sql, 'Knt_Ulica') &&
            str_contains($sql, 'Knt_NrDomu') &&
            str_contains($sql, 'Knt_NrLokalu') &&
            str_contains($sql, 'Knt_Nip') &&
            str_contains($sql, 'Knt_Nieaktywny')
        )
        ->andReturn([$row]);

    $this->repository->findByCode($row->code);
});

it('maps all fields from the database row', function () {
    $row = fakeCustomerRow([
        'id'              => 100,
        'code'            => 'TEST1',
        'company'         => 'Totem',
        'name_line_two'   => 'Line 2',
        'name_line_three' => 'Line 3',
        'country'         => 'PL',
        'city'            => 'Warsaw',
        'postal_code'     => '00-001',
        'street'          => 'ul. Marszałkowska',
        'building_number' => '10',
        'suite_number'    => '5A',
        'nip'             => '1234567890',
        'deleted'         => 0,
    ]);

    $this->connection->shouldReceive('select')->once()->andReturn([$row]);

    expect($this->repository->findByCode('TEST1'))
        ->id->toBe(100)
        ->code->toBe('TEST1')
        ->company->toBe('Totem')
        ->name_line_two->toBe('Line 2')
        ->name_line_three->toBe('Line 3')
        ->country->toBe('PL')
        ->city->toBe('Warsaw')
        ->postal_code->toBe('00-001')
        ->street->toBe('ul. Marszałkowska')
        ->building_number->toBe('10')
        ->suite_number->toBe('5A')
        ->nip->toBe('1234567890')
        ->deleted->toBeFalse();
});
