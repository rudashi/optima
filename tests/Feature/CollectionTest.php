<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\CollectionTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Tests\HelperClasses\CustomPrimaryDTO;
use Rudashi\Optima\Tests\HelperClasses\FakeDTO;
use Rudashi\Optima\Tests\TestCase;
use function Pest\Faker\faker;

uses(TestCase::class);

function makeArray(string $type = 'object', int $total = 3): array
{
    return array_map(function() use ($type){
        $array = [
            'id' => faker()->numberBetween(),
            'uuid' => faker()->uuid(),
            'email' => faker()->email(),
            'active' => 1
        ];
        settype($array, $type);
        return $array;
    }, range(0, $total - 1));
}
function dto(string $classname = FakeDTO::class): FakeDTO {
    return new $classname(
        id: faker()->numberBetween(1, 10),
        order_id: faker()->numberBetween(11, 100),
        name: faker()->name(),
        description: faker()->company()
    );
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
