<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\DTOTest;

use Rudashi\Optima\Tests\HelperClasses\CustomPrimaryDTO;
use Rudashi\Optima\Tests\HelperClasses\FakeDTO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('can get model key', function () {
    $dto = new FakeDTO(id: 3, order_id: 10, name: 'fake', description: 'desc');

    expect($dto)
        ->getKeyName()->toBe('id')
        ->getKey()->toBe(3);
});

it('can get custom model key', function () {
    $dto = new CustomPrimaryDTO(id: 3, order_id: 10, name: 'fake', description: 'desc');

    expect($dto)
        ->getKeyName()->toBe('order_id')
        ->getKey()->toBe(10);
});

it('can get attribute', function () {
    $dto = new FakeDTO(id: 3, order_id: 10, name: 'fake');

    expect($dto)
        ->getAttribute('id')->toBe(3)
        ->getAttribute('order_id')->toBe(10)
        ->getAttribute('fake')->toBeNull()
        ->getAttribute('name')->toBe('fake')
        ->getAttribute('description')->toBeNull();
});

it('can get all attributes', function () {
    $dto = new FakeDTO(id: 3, order_id: 10, name: 'fake');

    expect($dto->getAttributes())
        ->toMatchArray([
            'id' => 3,
            'order_id' => 10,
            'name' => 'fake',
            'description' => null,
        ]);
});

it('can determine whether an attribute is passed', function () {
    $attributes = [
        'id' => 3,
        'order_id' => 10,
        'name' => null,
    ];
    $dto = new FakeDTO(...$attributes);

    expect($dto)
        ->has('id', $attributes)->toBeTrue()
        ->has('order_id', $attributes)->toBeTrue()
        ->has('name', $attributes)->toBeFalse()
        ->has('description', $attributes)->toBeFalse();
});

it('can determine whether an attribute is filled', function () {
    $attributes = [
        'id' => 3,
        'order_id' => 10,
        'name' => null,
    ];
    $dto = new FakeDTO(...$attributes);

    expect($dto)
        ->filled('id', $attributes)->toBeTrue()
        ->filled('order_id', $attributes)->toBeTrue()
        ->filled('name', $attributes)->toBeTrue()
        ->filled('description', $attributes)->toBeFalse()
        ->filled('description')->toBeTrue();
});
