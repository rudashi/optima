<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Repositories\CustomerRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Enums\CustomerGroup;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('maps a customer found by code to a fully typed model', function (string $code) {
    expect(resolve(CustomerRepository::class)->findByCode($code))
        ->toBeInstanceOf(Customer::class)
        ->id->toBeInt()
        ->code->toBe($code)
        ->company->toBeString()
        ->name->toBeString()
        ->name_line_two->toBeNullableString()
        ->name_line_three->toBeNullableString()
        ->country->toBeNullableString()
        ->city->toBeNullableString()
        ->postal_code->toBeNullableString()
        ->street->toBeNullableString()
        ->building_number->toBeNullableString()
        ->suite_number->toBeNullableString()
        ->nip->toBeNullableString()
        ->deleted->toBeBool();
})->with([
    'TEST1',
    'TOTEM ZOO',
    'TOTEM TEST!',
]);

it('applies the group filter when a group is provided', function () {
    expect(resolve(CustomerRepository::class)->findByCode('ANTALIS', CustomerGroup::SUPPLIER->value))
        ->toBeInstanceOf(Customer::class)
        ->code->toBe('ANTALIS');
});

it('throws when the code exists only in another group', function () {
    expect(fn () => resolve(CustomerRepository::class)->findByCode('ANTALIS', CustomerGroup::SUBCONTRACTOR->value))
        ->toThrow(RecordsNotFoundException::class);
});

it('returns a typed Collection of customers when found by id', function () {
    $customers = resolve(CustomerRepository::class)->find(1, 4328, 26820, 5160);

    expect($customers->isEmpty())->toBeFalse()
        ->and($customers)
        ->toBeInstanceOf(Collection::class)
        ->each->toBeInstanceOf(Customer::class)
        ->and($customers->pluck('id')->all())->each->toBeInt();
});

it('throws when no customer matches the given code', function () {
    expect(fn () => resolve(CustomerRepository::class)->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('throws when no customer matches the given id', function () {
    expect(fn () => resolve(CustomerRepository::class)->find(999999999))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given id is invalid or not in the OPTIMA.'),
        );
});
