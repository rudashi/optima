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
            'name' => PaymentForm::BANK_TRANSFER_PLN->description(),
            'currency' => PaymentForm::BANK_TRANSFER_PLN->currency(),
            'value' => PaymentForm::BANK_TRANSFER_PLN->value,
        ])
        ->each->toHaveKeys(['value', 'name', 'currency']);
});
