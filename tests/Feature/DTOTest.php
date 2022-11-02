<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\DTOTest;

use DateTime;
use Rudashi\Optima\Services\DTO;
use Rudashi\Optima\Tests\HelperClasses\CustomPrimaryDTO;
use Rudashi\Optima\Tests\HelperClasses\FakeDTO;
use Rudashi\Optima\Tests\HelperClasses\FakeEnum;
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

it('can create instance from Array', function () {
    $attributes = [
        'id' => 3,
        'order_id' => 10,
        'name' => null,
    ];
    $dto = new FakeDTO($attributes);

    expect($dto->getAttributes())
        ->toMatchArray([
            'id' => 3,
            'order_id' => 10,
            'name' => null,
            'description' => null,
        ]);
});

it('can create instance from Object', function () {
    $attributes = (object) [
        'id' => 3,
        'order_id' => 10,
        'name' => null,
    ];
    $dto = new FakeDTO($attributes);

    expect($dto->getAttributes())
        ->toMatchArray([
            'id' => 3,
            'order_id' => 10,
            'name' => null,
            'description' => null,
        ]);
});

it('can cast property to other type', function () {
    $dto = new class extends DTO {
        public string $string;
        public DateTime $date;
        public FakeEnum $enum;
        public bool $bool;

        public function __construct(...$args)
        {
            $this->cast('enum', FakeEnum::class);
            $this->cast('string', fn($v) => trim($v));

            parent::__construct($args);
        }
    };

    expect(new $dto(
        bool: 3,
        string: '  10   ',
        date: '2022-02-21 13:42:20',
        enum: 'D',
    ))
        ->bool->toBe(true)
        ->string->toBe('10')
        ->date->toBeInstanceOf(DateTime::class)
        ->enum->toBe(FakeEnum::Diamonds);
});

it('can determine whether an attribute is filled', function () {
    $attributes = [
        'id' => 3,
        'order_id' => 0,
        'name' => null,
    ];

    $dto = new FakeDTO(...$attributes);

    expect($dto)
        ->filled('id', $attributes)->toBeTrue()
        ->filled('order_id', $attributes)->toBeTrue()
        ->filled('name', $attributes)->toBeTrue()
        ->filled('description', $attributes)->toBeFalse()
        ->filled('id')->toBeTrue()
        ->filled('order_id')->toBeTrue()
        ->filled('name')->toBeTrue()
        ->filled('description')->toBeTrue();

    $dto2 = new FakeDTO($attributes);

    expect($dto2)
        ->filled('id', (object) $attributes)->toBeTrue()
        ->filled('order_id', (object) $attributes)->toBeTrue()
        ->filled('name', (object) $attributes)->toBeTrue()
        ->filled('description', (object) $attributes)->toBeFalse();
});
