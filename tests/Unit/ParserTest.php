<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Unit;

use Rudashi\Optima\Exceptions\IncorrectValueException;
use Rudashi\Optima\Services\Entity\Parser;
use Rudashi\Optima\Tests\HelperClasses\FakeEnum;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Parser::class);

it('create Parser instance', function ($value) {
    $parser = new Parser($value);

    expect(array_values((array) $parser))
        ->toMatchArray([$value]);
})->with([
    'string' => fake()->name(),
    'int' => fake()->imei(),
    'float' => fake()->latitude(),
    'null' => null,
]);

it('create Parser from object', function () {
    $value = fake()->name();
    $parser = Parser::for((object) ['key' => $value], 'key');

    expect(array_values((array) $parser))
        ->toMatchArray([$value]);
});

it('get bool or null', function ($payload, $value): void {
    expect((new Parser($payload))->bool())
        ->toBe($value);
})->with([
    'bool when string' => [' some value   ', true],
    'false when empty string' => ['', false],
    'false when null' => [null, false],
]);

it('runs callback on value', function ($test, $expectation): void {
    $parser = new Parser(1);

    $result = $parser->call(fn ($value) => $value !== $test);

    expect($result)
        ->toBe($expectation);
})->with([
    'positive' => [1, false],
    'negative' => [-1, true],
]);

it('get Date or null', function ($payload, $value): void {
    expect((new Parser($payload))->date())
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

it('get enum or null', function ($payload, $value): void {
    expect((new Parser($payload))->enum(FakeEnum::class))
        ->toBe($value);
})->with([
    'enum when string' => ['H', FakeEnum::Hearts],
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

it('returns value', function () {
    $value = fake()->name();
    $parser = Parser::for((object) ['key' => $value], 'key');

    expect($parser->return())
        ->toMatchArray([$value]);
});

it('get string', function ($payload, $value): void {
    expect((new Parser($payload))->string())
        ->toBe($value);
})->with([
    'string when string' => [' some value   ', ' some value   '],
    'empty string when empty string' => ['', ''],
    'null when null' => [null, null],
]);

it('trim data', function ($payload, $value): void {
    expect((new Parser($payload))->trim())
        ->toBe($value);
})->with([
    'trim string when string' => [' some value   ', 'some value'],
    'null when empty string' => ['', null],
    'null when null' => [null, null],
]);

it('throws an exception on condition', function (): void {
    $parser = new Parser('');

    expect(fn () => $parser->throwWhen(fn () => true))
        ->toThrow(
            exception: IncorrectValueException::class,
            exceptionMessage: 'Incorrect value',
        );
});

it('throws an exception with custom message', function (): void {
    $parser = new Parser('');

    expect(fn () => $parser->throwWhen(fn () => true, 'Custom message'))
        ->toThrow(
            exception: IncorrectValueException::class,
            exceptionMessage: 'Custom message',
        );
});

it('not throws an exception on condition', function (): void {
    $parser = new Parser('');

    expect(fn () => $parser->throwWhen(fn ($v) => $v === null))
        ->not->toThrow(IncorrectValueException::class);
});

it('runs callback on non null value', function ($payload, $expectation): void {
    $parser = new Parser($payload);

    $result = $parser->whenNull(fn ($value) => $value === 1);

    expect($result)
        ->toBe($expectation);
})->with([
    'positive' => [1, true],
    'negative' => [-1, false],
    'null' => [null, null],
]);
