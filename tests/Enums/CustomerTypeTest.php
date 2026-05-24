<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CustomerTypeTest;

use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\CustomerType;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(CustomerType::class);

it('is an int-backed enum', function () {
    expect(CustomerType::RECIPIENT)->toBeInstanceOf(CustomerType::class)
        ->and(CustomerType::RECIPIENT->value)->toBeInt();
});

it('implements Describable contract', function () {
    expect(CustomerType::class)->toImplement(Describable::class);
});

it('has correct backed values', function (CustomerType $case, int $value) {
    expect($case->value)->toBe($value);
})->with([
    [CustomerType::RECIPIENT, 0],
    [CustomerType::SUPPLIER, 1],
    [CustomerType::COMPETITION, 2],
    [CustomerType::PARTNER, 3],
    [CustomerType::POTENTIAL, 4],
]);

it('can be created from an int value', function () {
    expect(CustomerType::from(0))->toBe(CustomerType::RECIPIENT)
        ->and(CustomerType::from(4))->toBe(CustomerType::POTENTIAL);
});

it('returns null from tryFrom for unknown value', function () {
    expect(CustomerType::tryFrom(5))->toBeNull()
        ->and(CustomerType::tryFrom(-1))->toBeNull();
});

it('returns correct description for each case', function (CustomerType $case, string $description) {
    expect($case->description())->toBeString()->not->toBeEmpty()
        ->and($case->description())->toBe($description);
})->with([
    [CustomerType::RECIPIENT, 'Recipient'],
    [CustomerType::SUPPLIER, 'Supplier'],
    [CustomerType::COMPETITION, 'Competition'],
    [CustomerType::PARTNER, 'Partner'],
    [CustomerType::POTENTIAL, 'Potential'],
]);

it('returns true when comparing equal cases via equals()', function () {
    expect(CustomerType::RECIPIENT->equals(CustomerType::RECIPIENT))->toBeTrue()
        ->and(CustomerType::SUPPLIER->equals(CustomerType::SUPPLIER))->toBeTrue();
});

it('returns false when comparing different cases via equals()', function () {
    expect(CustomerType::RECIPIENT->equals(CustomerType::SUPPLIER))->toBeFalse()
        ->and(CustomerType::PARTNER->equals(CustomerType::POTENTIAL))->toBeFalse();
});

it('has exactly five cases', function () {
    expect(CustomerType::cases())->toHaveCount(5);
});
