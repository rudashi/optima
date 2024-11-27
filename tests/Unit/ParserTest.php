<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Unit;

use Rudashi\Optima\Services\Entity\Parser;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Parser::class);

it('create Parser instance', function ($value) {
    expect(array_values((array) new Parser($value)))
        ->toMatchArray([$value]);
})->with([
    'string' => fake()->name(),
    'int' => fake()->imei(),
    'float' => fake()->latitude(),
    'null' => null,
]);

it('create Parser from object', function () {
    $value = fake()->name();

    expect(array_values((array) Parser::for((object) ['key' => $value], 'key')))
        ->toMatchArray([$value]);
});

it('trim data', function ($payload, $value): void {
    expect((new Parser($payload))->trim())
        ->toBe($value);
})->with([
    'trim string when string' => [' some value   ', 'some value'],
    'null when empty string' => ['', null],
    'null when null' => [null, null],
]);

it('get int or null', function ($payload, $value): void {
    expect((new Parser($payload))->int())
        ->toBe($value);
})->with([
    'int when string with number' => ['72', 72],
    'int when empty string' => ['', 0],
    'null when null' => [null, null],
]);

it('get float or null', function ($payload, $value): void {
    expect((new Parser($payload))->float())
        ->toBe($value);
})->with([
    'float when string with number' => ['72', 72.0],
    'float when empty string' => ['', 0.0],
    'null when null' => [null, null],
]);
