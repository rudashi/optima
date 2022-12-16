<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\CollectionTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Tests\HelperClasses\CustomPrimaryDTO;
use Rudashi\Optima\Tests\HelperClasses\FakeDTO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

function makeArray(string $type = 'object', int $total = 3): array
{
    return array_map(function () use ($type) {
        $array = [
            'id' => fake()->numberBetween(),
            'uuid' => fake()->uuid(),
            'email' => fake()->email(),
            'active' => 1,
        ];
        settype($array, $type);

        return $array;
    }, range(0, $total - 1));
}

function dto(string $classname = FakeDTO::class): FakeDTO
{
    return new $classname(
        id: fake()->numberBetween(1, 10),
        order_id: fake()->numberBetween(11, 100),
        name: fake()->name(),
        description: fake()->company()
    );
}

it('can pluck multiple values from nested object', function (array $array) {
    $collection = (new Collection($array))->pluckAll(['uuid', 'email']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(6);
})->with([fn () => makeArray()]);

it('can pluck multiple values from nested array', function (array $array) {
    $collection = (new Collection($array))->pluckAll(['uuid', 'email']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(6);
})->with([fn () => makeArray('array')]);

it('can pluck multiple values without duplicates', function (array $array) {
    $collection = (new Collection($array))->pluckAll(['uuid', 'active']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(4);
})->with([fn () => makeArray()]);

it('can get only model keys', function () {
    $first = dto();
    $second = dto(CustomPrimaryDTO::class);
    $third = dto();
    $collection = (new Collection([$first, $second, $third]));

    expect($collection->modelKeys())
        ->toBeArray()
        ->toHaveCount(3)
        ->toMatchArray([$first->id, $second->order_id, $third->id]);
});

it('can attach to collection item', function () {
    $collection = (new Collection(makeArray('array', 2)));

    expect($collection)
        ->sequence(
            fn ($item) => $item->not->toHaveKey('y', 'new'),
        );

    $collection->attach(static function ($item) {
        $item['y'] = 'new';
        return $item;
    });

    expect($collection)
        ->sequence(
            fn ($item) => $item->toHaveKey('y', 'new'),
        );
});

it('can attach to collection item other filtered collection', function () {
    $collection = (new Collection(makeArray('array')));
    $second = (new Collection($collection->take(2)));

    $collection->attach(function ($item) use ($second) {
        $item['x'] = $second
            ->filter(static fn ($y) => $y['id'] === $item['id'])
            ->pluckAll(['id', 'uuid']);
        return $item;
    });

    expect($collection)
        ->sequence(
            fn ($item) => $item->x->toBeArray()->toHaveCount(2),
            fn ($item) => $item->x->toBeArray()->toHaveCount(2),
            fn ($item) => $item->x->toBeArray()->toBeEmpty(),
        );
});
