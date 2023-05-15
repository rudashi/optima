<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\DTOTest;

use Carbon\Carbon;
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
    $dto = new class () extends DTO {
        public string $string;
        public DateTime $date;
        public FakeEnum $enum;
        public bool $bool;

        public function __construct(...$args)
        {
            $this->cast('enum', FakeEnum::class);
            $this->cast('string', fn ($v) => trim($v));

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
        ->date->toBeInstanceOf(Carbon::class)
        ->enum->toBe(FakeEnum::Diamonds);
});

it('casts `\Illuminate\Support\Carbon` to self instance', function () {
    $dto = new class () extends DTO {
        public \Illuminate\Support\Carbon $date;
    };

    expect((new $dto(date: '2022-02-21 13:42:20'))->date)
        ->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

it('can determine whether an attribute is filled', function () {
    $attributes = ['id' => 3, 'order_id' => 0, 'name' => null];

    expect(new FakeDTO())
        ->filled('id', $attributes)->toBeTrue()
        ->filled('order_id', $attributes)->toBeTrue()
        ->filled('name', $attributes)->toBeTrue()
        ->filled('description', $attributes)->toBeFalse();

    $attributes = (object) $attributes;

    expect(new FakeDTO())
        ->filled('id', $attributes)->toBeTrue()
        ->filled('order_id', $attributes)->toBeTrue()
        ->filled('description', $attributes)->toBeFalse()
        ->filled('name', $attributes)->toBeTrue();
});

it('can get from value from passed attributes', function () {
    $attributes = ['id' => 3, 'order_id' => 0, 'name' => null];

    expect(new FakeDTO())
        ->get('id', $attributes)->toBe(3)
        ->get('order_id', $attributes)->toBe(0)
        ->get('name', $attributes)->toBe(null)
        ->get('fake', $attributes)->toBe(null);

    $attributes = (object) $attributes;

    expect(new FakeDTO())
        ->get('id', $attributes)->toBe(3)
        ->get('order_id', $attributes)->toBe(0)
        ->get('fake', $attributes)->toBe(null)
        ->get('name', $attributes)->toBe(null);
});

it('can get the full object as an array', function () {
    $attributes = [
        'id' => 3,
        'order_id' => 10,
        'name' => null,
    ];
    $dto = new FakeDTO($attributes);

    expect($dto->all())
        ->toMatchArray([
            'id' => 3,
            'order_id' => 10,
            'name' => null,
            'description' => null,
        ]);
});

it('can get some properties from an object as an array', function () {
    $attributes = [
        'id' => 3,
        'order_id' => 10,
        'name' => null,
    ];
    $dto = new FakeDTO($attributes);
    $dto->only('id', 'name');

    expect($dto->toArray())
        ->toMatchArray([
            'id' => 3,
            'name' => null,
        ]);
});

it('can append property', function () {
    $dto = new class () extends DTO {
        public string $string;
        public FakeEnum $enum;

        public function __construct(...$args)
        {
            if (count($args)) {
                $this->append('enum', fn () => FakeEnum::tryFrom((string) self::get('date', $args)));
            }

            parent::__construct($args);
        }
    };

    expect(new $dto(
        string: '10',
        date: 'D',
    ))
        ->string->toBe('10')
        ->not->toHaveProperty('date')
        ->enum->toBe(FakeEnum::Diamonds);
});
