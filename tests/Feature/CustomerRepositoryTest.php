<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\CustomerRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Enums\CustomerType;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

function repository(): CustomerRepository {
    return app(CustomerRepository::class);
}

$customers = [
    1 => [
        [
            'id' => 1,
            'code' => '!NIEOKREŚLONY!',
            'name' => '!NIEOKREŚLONY!',
            'name_line_two' => null,
            'name_line_three' => null,
            'city' => null,
            'suite_number' => null,
        ]
    ],
    4328 => [
        [
            'id' => 4328,
            'code' => 'TEST1',
            'name' => 'test1',
            'name_line_two' => null,
            'name_line_three' => null,
            'city' => 'Test',
            'suite_number' => null,
        ]
    ],
    26820 => [
        [
            'id' => 26820,
            'code' => 'TOTEM TEST!',
            'name' => 'totem',
            'name_line_two' => null,
            'name_line_three' => null,
            'city' => null,
            'suite_number' => null,
        ]
    ],
    5160 => [
        [
            'id' => 5160,
            'code' => 'TOTEM ZOO',
            'name' => 'TOTEM.COM.PL SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            'name_line_two' => 'SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            'name_line_three' => null,
            'city' => 'Inowrocław',
            'suite_number' => null,
        ]
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
    ]
];

it('can find a customer by code', function (array $dataset) {

    $data = repository()->findByCode($dataset['code']);

    expect($data)
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperty('id', $dataset['id'])
        ->toHaveProperty('code', $dataset['code'])
        ->toHaveProperty('name', $dataset['name'])
        ->toHaveProperty('name_line_two', $dataset['name_line_two'])
        ->toHaveProperty('name_line_three', null)
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

it('throws an exception when customer code not exists', function () {

    $this->expectExceptionMessage(__('Given code :code is invalid or not in the OPTIMA.', ['code' => '']));

    repository()->findByCode('');

})->throws(RecordsNotFoundException::class);

it('can find a grouped customer by code from group', function (string $code, array $dataset) {

    $data = repository()->findByCode($code, CustomerType::SUPPLIER->value);

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

    $this->expectExceptionMessage(__('Given code :code is invalid or not in the OPTIMA.', ['code' => $code]));

    repository()->findByCode($code, CustomerType::SUBCONTRACTOR->value);

})->with([$supplier])->throws(RecordsNotFoundException::class);

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
        ->sequence(function($item, $key) use ($customers) {
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

    $this->expectExceptionMessage(__('Given id is invalid or not in the OPTIMA.'));

    repository()->find(null);

})->throws(RecordsNotFoundException::class);
