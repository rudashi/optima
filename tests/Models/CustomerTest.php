<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Models\CustomerTest;

use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Customer::class);

it('maps all fields from the database row', function () {
    $customer = Customer::make(fakeCustomerRow([
        'id'              => '7',
        'code'            => 'TEST1',
        'company'         => 'ACME',
        'name_line_two'   => 'Sp. z o.o.',
        'name_line_three' => 'Oddział',
        'country'         => 'Polska',
        'city'            => 'Kwidzyn',
        'postal_code'     => '82-500',
        'street'          => 'ul. Polna',
        'building_number' => '26',
        'suite_number'    => '1',
        'nip'             => '1234567890',
        'deleted'         => 0,
    ]));

    expect($customer)
        ->id->toBe(7)
        ->code->toBe('TEST1')
        ->company->toBe('ACME')
        ->name_line_two->toBe('Sp. z o.o.')
        ->name_line_three->toBe('Oddział')
        ->name->toBe('ACME Sp. z o.o. Oddział')
        ->country->toBe('Polska')
        ->city->toBe('Kwidzyn')
        ->postal_code->toBe('82-500')
        ->street->toBe('ul. Polna')
        ->building_number->toBe('26')
        ->suite_number->toBe('1')
        ->nip->toBe('1234567890')
        ->deleted->toBeFalse();
});

it('casts deleted to a boolean', function (int $deleted, bool $expected) {
    expect(Customer::make(fakeCustomerRow(['deleted' => $deleted]))->deleted)->toBe($expected);
})->with([
    'truthy' => [1, true],
    'falsy'  => [0, false],
]);

it('trims the name when name lines are missing', function () {
    $customer = Customer::make(fakeCustomerRow(['company' => 'ACME']));

    expect($customer->name)->toBe('ACME');
});

it('keeps a double space in the name when only the third line is set', function () {
    $customer = Customer::make(fakeCustomerRow([
        'company'         => 'ACME',
        'name_line_three' => 'North',
    ]));

    expect($customer->name)->toBe('ACME  North');
});

it('normalizes the city capitalization', function (?string $city, ?string $expected) {
    expect(Customer::make(fakeCustomerRow(['city' => $city]))->city)->toBe($expected);
})->with([
    'uppercase'           => ['KWIDZYN', 'Kwidzyn'],
    'lowercase'           => ['gdańsk', 'Gdańsk'],
    'multibyte first char' => ['ŁÓDŹ', 'łódź'],
    'null'                => [null, null],
]);

it('formats the street name', function (?string $street, ?string $expected) {
    expect(Customer::make(fakeCustomerRow(['street' => $street]))->street)->toBe($expected);
})->with([
    'ul prefix'             => ['ul. Polna', 'ul. Polna'],
    'ulica prefix'          => ['ulica Polna', 'ulica Polna'],
    'lowercase'             => ['polna', 'Polna'],
    'uppercase'             => ['POLNA ZACHODNIA', 'Polna Zachodnia'],
    'capitalized Ul prefix' => ['Ul. Polna', 'Ul. Polna'],
    'ul inside the name'    => ['ulanowska', 'ulanowska'],
    'null'                  => [null, null],
]);

it('trims the country and postal code', function () {
    $customer = Customer::make(fakeCustomerRow([
        'country'     => ' Polska ',
        'postal_code' => ' 82-500 ',
    ]));

    expect($customer->country)->toBe('Polska')
        ->and($customer->postal_code)->toBe('82-500');
});

it('returns null for empty optional fields', function () {
    $customer = Customer::make(fakeCustomerRow([
        'country'         => '',
        'postal_code'     => '',
        'building_number' => '',
        'suite_number'    => '',
    ]));

    expect($customer->country)->toBeNull()
        ->and($customer->postal_code)->toBeNull()
        ->and($customer->building_number)->toBeNull()
        ->and($customer->suite_number)->toBeNull();
});

it('handles rows without optional columns', function () {
    $row = fakeCustomerRow();
    unset($row->country, $row->nip);

    $customer = Customer::make($row);

    expect($customer->country)->toBeNull()
        ->and($customer->nip)->toBeNull();
});
