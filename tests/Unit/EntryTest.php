<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Unit;

use Rudashi\Optima\Services\Entity\Entry;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Entry::class);

it('trim data', function ($payload, $value): void {
    expect(Entry::trim($payload))
        ->toBe($value);
})->with([
    'trim string when string' => [' some value   ', 'some value'],
    'null when empty string' => ['', null],
    'null when null' => [null, null],
]);

it('get int or null', function ($payload, $value): void {
    expect(Entry::int($payload))
        ->toBe($value);
})->with([
    'int when string with number' => ['72', 72],
    'int when empty string' => ['', 0],
    'null when null' => [null, null],
]);

it('get float or null', function ($payload, $value): void {
    expect(Entry::float($payload))
        ->toBe($value);
})->with([
    'float when string with number' => ['72', 72.0],
    'float when empty string' => ['', 0.0],
    'null when null' => [null, null],
]);

it('get Date or null', function ($payload, $value): void {
    expect(Entry::date($payload))
        ->when(
            condition: $payload !== null,
            callback: fn ($exp) => $exp->format('Y-m-d')->toBe($value)
        )
        ->when(
            condition: $payload === null,
            callback: fn ($exp) => $exp->toBeNull()
        );
})->with([
    'date when string' => ['2022-01-31 20:03:11', '2022-01-31'],
    'date when empty string' => ['', now()->format('Y-m-d')],
    'null when null' => [null, null],
]);
