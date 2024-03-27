<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Unit\CountryTest;

use Rudashi\Optima\Enums\PaymentForm;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('can return list of Payments', function () {
    $data = PaymentForm::toSelect();

    expect($data)
        ->toBeArray()
        ->toHaveCount(6)
        ->toContain([
            'name' => PaymentForm::BANK_TRANSFER_PL->description(),
            'value' => PaymentForm::BANK_TRANSFER_PL->value,
        ])
        ->not->toContain([
            'name' => PaymentForm::CASH_PL->description(),
            'value' => PaymentForm::BANK_TRANSFER_PL->value,
        ]);
});
