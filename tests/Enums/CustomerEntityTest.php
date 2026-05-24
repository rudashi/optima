<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CustomerEntityTest;

use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\CustomerEntity;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(CustomerEntity::class);

it('is an int-backed enum', function () {
    expect(CustomerEntity::COMPANY)->toBeInstanceOf(CustomerEntity::class)
        ->and(CustomerEntity::COMPANY->value)->toBeInt();
});

it('implements Describable contract', function () {
    expect(CustomerEntity::class)->toImplement(Describable::class);
});

it('has correct backed values', function (CustomerEntity $case, int $value) {
    expect($case->value)->toBe($value);
})->with([
    [CustomerEntity::COMPANY, 0],
    [CustomerEntity::PERSON, 1],
]);

it('can be created from an int value', function () {
    expect(CustomerEntity::from(0))->toBe(CustomerEntity::COMPANY)
        ->and(CustomerEntity::from(1))->toBe(CustomerEntity::PERSON);
});

it('returns null from tryFrom for unknown value', function () {
    expect(CustomerEntity::tryFrom(2))->toBeNull()
        ->and(CustomerEntity::tryFrom(-1))->toBeNull();
});

it('returns correct description for each case', function (CustomerEntity $case, string $description) {
    expect($case->description())->toBeString()->not->toBeEmpty()
        ->and($case->description())->toBe($description);
})->with([
    [CustomerEntity::COMPANY, 'Business entity'],
    [CustomerEntity::PERSON, 'Natural person'],
]);

it('has exactly two cases', function () {
    expect(CustomerEntity::cases())->toHaveCount(2);
});
