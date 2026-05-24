<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\PaymentFormTest;

use Rudashi\Optima\Enums\PaymentForm;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(PaymentForm::class);

it('can return list of Payments', function () {
    $data = PaymentForm::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(25)
        ->toContain([
            'name' => PaymentForm::BANK_TRANSFER_PLN->description(),
            'currency' => PaymentForm::BANK_TRANSFER_PLN->currency(),
            'value' => PaymentForm::BANK_TRANSFER_PLN->value,
        ])
        ->each->toHaveKeys(['value', 'name', 'currency']);
});
