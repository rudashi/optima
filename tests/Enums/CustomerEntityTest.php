<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CustomerEntityTest;

use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\CustomerEntity;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(CustomerEntity::class);

it('implements Describable contract', function () {
    expect(CustomerEntity::class)->toImplement(Describable::class);
});

it('has correct backed values', function (CustomerEntity $case, int $value) {
    expect($case->value)->toBe($value);
})->with([
    [CustomerEntity::COMPANY, 0],
    [CustomerEntity::PERSON, 1],
]);

it('returns correct description for each case', function (CustomerEntity $case, string $description) {
    expect($case->description())->toBe($description);
})->with([
    [CustomerEntity::COMPANY, 'Business entity'],
    [CustomerEntity::PERSON, 'Natural person'],
]);
