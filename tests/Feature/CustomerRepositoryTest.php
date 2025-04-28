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

dataset('customers', [
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
]);

beforeEach(function () {
    $this->repository = resolve(CustomerRepository::class);
});

it('can find a customer by code', function (array $dataset) {
    $data = $this->repository->findByCode($dataset['code']);

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
        ]);
})->with('customers');

it('throws an exception when customer code not exists', function () {
    expect(fn () => $this->repository->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('can find a grouped customer by code from group', function () {
    $data = $this->repository->findByCode('ANTALIS', CustomerGroup::SUPPLIER->value);

    expect($data)
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperties([
            'id',
            'code' => 'ANTALIS',
            'company',
            'name' => 'ANTALIS POLAND SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            'name_line_two' => 'ODPOWIEDZIALNOŚCIĄ',
            'name_line_three' => null,
            'country',
            'city' => 'Warszawa',
            'postal_code',
            'street',
            'building_number',
            'suite_number' => null,
            'nip',
            'deleted',
        ]);
});

it('can find a grouped customer by code without group', function () {
    $data = $this->repository->findByCode('ANTALIS');

    expect($data)
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperties([
            'id',
            'code' => 'ANTALIS',
            'company',
            'name' => 'ANTALIS POLAND SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            'name_line_two' => 'ODPOWIEDZIALNOŚCIĄ',
            'name_line_three' => null,
            'country',
            'city' => 'Warszawa',
            'postal_code',
            'street',
            'building_number',
            'suite_number' => null,
            'nip',
            'deleted',
        ]);
});

it('throws an exception when grouped customer code is in other group', function () {
    expect(fn () => $this->repository->findByCode('ANTALIS', CustomerGroup::SUBCONTRACTOR->value))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => 'ANTALIS']),
        );
});

it('can find customers by ID', function (array $dataset) {
    $data = $this->repository->find($dataset['id']);

    expect($data)
        ->toBeInstanceOf(Collection::class)
        ->first()
        ->toBeInstanceOf(Customer::class)
        ->toHaveProperties([
            'id' => $dataset['id'],
            'code' => $dataset['code'],
            'company',
            'name' => $dataset['name'],
            'name_line_two' => $dataset['name_line_two'],
            'name_line_three' => $dataset['name_line_three'],
            'country',
            'city' => $dataset['city'],
            'postal_code',
            'street',
            'building_number',
            'suite_number' => $dataset['suite_number'],
            'nip',
            'deleted',
        ]);
})->with('customers');

it('can find multiple customers by ID', function () {
    $data = $this->repository->find(1, 4328, 26820, 5160);

    expect($data)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(4);
});

it('throws an exception when customer id not exists', function () {
    expect(fn () => $this->repository->find(null))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given id is invalid or not in the OPTIMA.'),
        );
});
