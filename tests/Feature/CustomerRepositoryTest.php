<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\CustomerRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Enums\CustomerGroup;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

function repository(): CustomerRepository
{
    return app(CustomerRepository::class);
}

$customers = [
    1 => [
        [
            'id' => 1,
            'code' => '!NIEOKREŚLONY!',
            'company' => '!NIEOKREŚLONY!',
            'name' => '!NIEOKREŚLONY!',
            'name_line_two' => null,
            'name_line_three' => null,
            'city' => null,
            'country' => null,
            'suite_number' => null,
        ],
    ],
    4328 => [
        [
            'id' => 4328,
            'code' => 'TEST1',
            'company' => 'test1',
            'name' => 'test1',
            'name_line_two' => null,
            'name_line_three' => null,
            'city' => 'Test',
            'street' => 'Test',
            'postal_code' => '88-100',
            'country' => 'Polska',
            'suite_number' => null,
        ],
    ],
    26820 => [
        [
            'id' => 26820,
            'code' => 'TOTEM TEST!',
            'company' => 'totem',
            'name' => 'totem',
            'name_line_two' => null,
            'name_line_three' => null,
            'city' => null,
            'street' => 'Jacewska',
            'country' => 'Polska',
            'building_number' => '89',
            'suite_number' => null,
        ],
    ],
    5160 => [
        [
            'id' => 5160,
            'code' => 'TOTEM ZOO',
            'company' => 'TOTEM.COM.PL',
            'name' => 'TOTEM.COM.PL SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            'name_line_two' => 'SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            'name_line_three' => null,
            'city' => 'Inowrocław',
            'street' => 'Jacewska',
            'postal_code' => '88-100',
            'country' => 'Polska',
            'building_number' => '89',
            'nip' => '5562753569',
            'suite_number' => null,
        ],
    ],
];
$supplier = [
    'ANTALIS',
    [
        'name' => 'ANTALIS Poland Sp. z o.o.',
        'name_line_two' => null,
        'name_line_three' => null,
        'city' => 'Warszawa',
        'suite_number' => null,
    ],
];

it('can find a customer by code', function (array $dataset) {
    $data = repository()->findByCode($dataset['code']);

    expect($data)
        ->toBeInstanceOf(Customer::class)
        ->toMatchArray([
            'id' => $dataset['id'],
            'code' => $dataset['code'],
            'company' => $dataset['company'],
            'name' => $dataset['name'],
            'name_line_two' => $dataset['name_line_two'],
            'name_line_three' => $dataset['name_line_three'],
            'country' => $dataset['country'],
            'city' => $dataset['city'],
            'postal_code' => $dataset['postal_code'] ?? null,
            'street' => $dataset['street'] ?? null,
            'building_number' => $dataset['building_number'] ?? '',
            'suite_number' => $dataset['suite_number'],
            'nip' => $dataset['nip'] ?? null,
            'email_warehouse' => null,
            'shipping_notes' => null,
            'deleted' => false,
        ]);
})->with($customers);

it('throws an exception when customer code not exists', function () {
    expect(fn () => repository()->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('can find a grouped customer by code from group', function (string $code, array $dataset) {
    $data = repository()->findByCode($code, CustomerGroup::SUPPLIER->value);

    expect($data)
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperty('code', $code)
        ->toHaveProperty('name', $dataset['name'])
        ->toHaveProperty('name_line_two', $dataset['name_line_two'])
        ->toHaveProperty('name_line_three', $dataset['name_line_three'])
        ->toHaveProperty('city', $dataset['city'])
        ->toHaveProperty('suite_number', $dataset['suite_number'])
        ->toHaveProperties([
            'id',
            'code',
            'company',
            'name',
            'name_line_two',
            'name_line_three',
            'country',
            'city',
            'postal_code',
            'street',
            'building_number',
            'suite_number',
            'nip',
            'deleted',
        ]);
})->with([$supplier]);

it('can find a grouped customer by code without group', function (string $code, array $dataset) {
    $data = repository()->findByCode($code);

    expect($data)
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperty('code', $code)
        ->toHaveProperty('name', $dataset['name'])
        ->toHaveProperty('name_line_two', $dataset['name_line_two'])
        ->toHaveProperty('name_line_three', $dataset['name_line_three'])
        ->toHaveProperty('city', $dataset['city'])
        ->toHaveProperty('suite_number', $dataset['suite_number'])
        ->toHaveProperties([
            'id',
            'code',
            'company',
            'name',
            'name_line_two',
            'name_line_three',
            'country',
            'city',
            'postal_code',
            'street',
            'building_number',
            'suite_number',
            'nip',
            'deleted',
        ]);
})->with([$supplier]);

it('throws an exception when grouped customer code is in other group', function (string $code) {
    expect(fn () => repository()->findByCode($code, CustomerGroup::SUBCONTRACTOR->value))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => $code]),
        );
})->with([$supplier]);

it('can find customers by ID', function (array $dataset) {
    $data = repository()->find($dataset['id']);

    expect($data)
        ->toBeInstanceOf(Collection::class)
    ->and($data->first())
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperty('id', $dataset['id'])
        ->toHaveProperty('code', $dataset['code'])
        ->toHaveProperty('name', $dataset['name'])
        ->toHaveProperty('name_line_two', $dataset['name_line_two'])
        ->toHaveProperty('name_line_three', $dataset['name_line_three'])
        ->toHaveProperty('city', $dataset['city'])
        ->toHaveProperty('suite_number', $dataset['suite_number'])
        ->toHaveProperties([
            'id',
            'code',
            'company',
            'name',
            'name_line_two',
            'name_line_three',
            'country',
            'city',
            'postal_code',
            'street',
            'building_number',
            'suite_number',
            'nip',
            'deleted',
        ]);
})->with($customers);

it('can find multiple customers by ID', function () use ($customers) {
    $data = repository()->find(array_keys($customers));

    expect($data)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(4)
        ->sequence(function ($item, $key) use ($customers) {
            $item->toBeInstanceOf(Customer::class)
                ->toHaveProperty('id', $customers[$key->value][0]['id'])
                ->toHaveProperty('code', $customers[$key->value][0]['code'])
                ->toHaveProperty('name', $customers[$key->value][0]['name'])
                ->toHaveProperty('name_line_two', $customers[$key->value][0]['name_line_two'])
                ->toHaveProperty('name_line_three', $customers[$key->value][0]['name_line_three'])
                ->toHaveProperty('city', $customers[$key->value][0]['city'])
                ->toHaveProperty('suite_number', $customers[$key->value][0]['suite_number'])
                ->toHaveProperties([
                    'id',
                    'code',
                    'company',
                    'name',
                    'name_line_two',
                    'name_line_three',
                    'country',
                    'city',
                    'postal_code',
                    'street',
                    'building_number',
                    'suite_number',
                    'nip',
                    'deleted',
                ]);
        });
});

it('throws an exception when customer id not exists', function () {
    expect(fn () => repository()->find(null))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given id is invalid or not in the OPTIMA.'),
        );
});
