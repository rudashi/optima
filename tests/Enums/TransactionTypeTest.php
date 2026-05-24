<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\TransactionTypeTest;

use Rudashi\Optima\Contracts\Arrayable;
use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\TransactionType;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(TransactionType::class);

it('is an int-backed enum', function () {
    expect(TransactionType::NATIONAL)->toBeInstanceOf(TransactionType::class)
        ->and(TransactionType::NATIONAL->value)->toBeInt();
});

it('implements Arrayable and Describable contracts', function () {
    expect(TransactionType::class)
        ->toImplement(Arrayable::class)
        ->toImplement(Describable::class);
});

it('has correct backed values', function (TransactionType $case, int $value) {
    expect($case->value)->toBe($value);
})->with([
    [TransactionType::NATIONAL, 0],
    [TransactionType::NON_EU, 1],
    [TransactionType::NON_EU_VAT_REFUND, 2],
    [TransactionType::INTRA_EU, 3],
    [TransactionType::INTRA_EU_TRIPARTITE, 4],
    [TransactionType::BUYER, 5],
    [TransactionType::EXTERNAL, 6],
    [TransactionType::EXTERNAL_NP, 7],
    [TransactionType::INTRA_EU_BUYER, 8],
    [TransactionType::NON_EU_BUYER, 9],
    [TransactionType::OSS, 10],
]);

it('can be created from an int value', function () {
    expect(TransactionType::from(0))->toBe(TransactionType::NATIONAL)
        ->and(TransactionType::from(10))->toBe(TransactionType::OSS);
});

it('returns null from tryFrom for unknown value', function () {
    expect(TransactionType::tryFrom(99))->toBeNull()
        ->and(TransactionType::tryFrom(-1))->toBeNull();
});

it('returns correct description for each case', function (TransactionType $case, string $description) {
    expect($case->description())->toBe($description);
})->with([
    [TransactionType::NATIONAL, 'Krajowy'],
    [TransactionType::NON_EU, 'Pozaunijny'],
    [TransactionType::NON_EU_VAT_REFUND, 'Pozaunijny (zwrot VAT)'],
    [TransactionType::INTRA_EU, 'Wewnątrzunijny'],
    [TransactionType::INTRA_EU_TRIPARTITE, 'Wewnątrzunijny trójstronny'],
    [TransactionType::BUYER, 'Podatnikiem jest nabywca'],
    [TransactionType::EXTERNAL, 'Poza terytorium kraju'],
    [TransactionType::EXTERNAL_NP, 'Poza terytorium kraju (stawka NP)'],
    [TransactionType::INTRA_EU_BUYER, 'Wewnątrzunijny — podatnikiem jest nabywca'],
    [TransactionType::NON_EU_BUYER, 'Pozaunijny — podatnikiem jest nabywca'],
    [TransactionType::OSS, 'Procedura OSS'],
]);

it('can return list of transactions as array', function () {
    $data = TransactionType::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(11)
        ->each->toHaveKeys(['name', 'value']);
});

it('includes national with correct data in toArray()', function () {
    expect(TransactionType::toArray())->toContain([
        'name' => TransactionType::NATIONAL->description(),
        'value' => TransactionType::NATIONAL->value,
    ]);
});

it('does not mix up cases in toArray()', function () {
    expect(TransactionType::toArray())->not->toContain([
        'name' => TransactionType::NATIONAL->description(),
        'value' => TransactionType::OSS->value,
    ]);
});
