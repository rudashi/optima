<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Schema\SeededDataTest;

use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Facades\DB;
use Rudashi\Optima\Enums\CustomerGroup;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('fixtures');

it('finds a customer by code from seeded fixture', function () {
    $result = DB::connection('optima')
        ->table('CDN.Kontrahenci')
        ->where('Knt_Kod', 'TEST-A')
        ->where('Knt_Nieaktywny', 0)
        ->first();

    expect($result)
        ->not->toBeNull()
        ->and($result->Knt_Nazwa1)->toBe('Test Company A');
});

it('maps a fully populated customer through the repository', function () {
    $repository = new CustomerRepository(app(OptimaService::class));

    expect($repository->findByCode('TEST-FULL'))
        ->toBeInstanceOf(Customer::class)
        ->id->toBe(4)
        ->code->toBe('TEST-FULL')
        ->company->toBe('Test Company Full')
        ->name_line_two->toBe('Sp. z o.o.')
        ->name_line_three->toBe('Oddział')
        ->name->toBe('Test Company Full Sp. z o.o. Oddział')
        ->country->toBe('Polska')
        ->city->toBe('Gdańsk')
        ->postal_code->toBe('82-500')
        ->street->toBe('ul. Polna')
        ->building_number->toBe('26')
        ->suite_number->toBe('1')
        ->nip->toBe('5860001234')
        ->deleted->toBeFalse();
});

it('applies the group filter when finding a customer by code', function () {
    $repository = new CustomerRepository(app(OptimaService::class));

    expect($repository->findByCode('TEST-FULL', CustomerGroup::SUBCONTRACTOR->value))
        ->code->toBe('TEST-FULL');

    expect(fn () => $repository->findByCode('TEST-FULL', CustomerGroup::SUPPLIER->value))
        ->toThrow(RecordsNotFoundException::class);
});
