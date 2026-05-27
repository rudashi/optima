<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Services\CollectionTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Tests\Fixtures\CustomPrimaryDTO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Collection::class);

it('can pluck multiple values from nested object', function (array $array) {
    $collection = (new Collection($array))->pluckAll(['uuid', 'email']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(6);
})->with([fn () => fakeCollectionArray()]);

it('can pluck multiple values from nested array', function (array $array) {
    $collection = (new Collection($array))->pluckAll(['uuid', 'email']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(6);
})->with([fn () => fakeCollectionArray('array')]);

it('can pluck multiple values without duplicates', function (array $array) {
    $collection = (new Collection($array))->pluckAll(['uuid', 'active']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(4);
})->with([fn () => fakeCollectionArray()]);

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
    $collection = (new Collection(fakeCollectionArray('array', 2)));

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
    $collection = (new Collection(fakeCollectionArray('array')));
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
