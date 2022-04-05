<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\CollectionTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Tests\TestCase;
use function Pest\Faker\faker;

uses(TestCase::class);

function makeArray(string $type = 'object', int $total = 3): array
{
    return array_map(function() use ($type){
        $array = [
            'uuid' => faker()->uuid,
            'email' => faker()->email,
            'active' => 1
        ];
        settype($array, $type);
        return $array;
    }, range(0, $total - 1));
}

it('can pluck multiple values from nested object', function (array $array) {

    $collection = (new Collection($array))->pluckAll(['uuid', 'email']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(6);
})->with([fn() => makeArray()]);

it('can pluck multiple values from nested array', function (array $array) {

    $collection = (new Collection($array))->pluckAll(['uuid', 'email']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(6);
})->with([fn() => makeArray('array')]);

it('can pluck multiple values without duplicates', function (array $array) {

    $collection = (new Collection($array))->pluckAll(['uuid', 'active']);

    expect($collection)
        ->toBeArray()
        ->toHaveCount(4);
})->with([fn() => makeArray()]);
