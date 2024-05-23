<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Unit\CountryTest;

use Rudashi\Optima\Enums\TransactionType;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('can return list of Transactions', function () {
    $data = TransactionType::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(11)
        ->toContain([
            'name' => TransactionType::NATIONAL->description(),
            'value' => TransactionType::NATIONAL->value,
        ])
        ->not->toContain([
            'name' => TransactionType::NATIONAL->description(),
            'value' => TransactionType::OSS->value,
        ]);
});
