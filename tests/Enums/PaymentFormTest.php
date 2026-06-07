<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\PaymentFormTest;

use Rudashi\Optima\Contracts\Arrayable;
use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\Country;
use Rudashi\Optima\Enums\PaymentForm;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(PaymentForm::class);

it('is an int-backed enum', function () {
    expect(PaymentForm::CASH_PLN)->toBeInstanceOf(PaymentForm::class)
        ->and(PaymentForm::CASH_PLN->value)->toBeInt();
});

it('implements Arrayable and Describable contracts', function () {
    expect(PaymentForm::class)
        ->toImplement(Arrayable::class)
        ->toImplement(Describable::class);
});

it('has correct backed values', function (PaymentForm $case, int $value) {
    expect($case->value)->toBe($value);
})->with([
    [PaymentForm::CASH_PLN, 1],
    [PaymentForm::PREPAYMENT_PLN, 19],
    [PaymentForm::BANK_TRANSFER, 36],
    [PaymentForm::BANK_TRANSFER_PLN, 44],
    [PaymentForm::PREPAYMENT, 46],
    [PaymentForm::BANK_TRANSFER_EUR_E, 55],
]);

it('can be created from an int value', function () {
    expect(PaymentForm::from(1))->toBe(PaymentForm::CASH_PLN)
        ->and(PaymentForm::from(36))->toBe(PaymentForm::BANK_TRANSFER);
});

it('returns null from tryFrom for unknown value', function () {
    expect(PaymentForm::tryFrom(0))->toBeNull()
        ->and(PaymentForm::tryFrom(999))->toBeNull();
});

it('returns correct description for each case', function (PaymentForm $case, string $description) {
    expect($case->description())->toBe($description);
})->with([
    [PaymentForm::CASH_PLN, 'gotówka'],
    [PaymentForm::PREPAYMENT_PLN, 'przedpłata'],
    [PaymentForm::BANK_TRANSFER, 'bank transfer'],
    [PaymentForm::BANK_TRANSFER_PLN, 'przelew bankowy'],
    [PaymentForm::PREPAYMENT, 'prepayment'],
]);

it('returns correct currency for currency-specific cases', function (PaymentForm $case, string $currency) {
    expect($case->currency())->toBe($currency);
})->with([
    [PaymentForm::CASH_PLN, 'PLN'],
    [PaymentForm::BANK_TRANSFER_PLN, 'PLN'],
    [PaymentForm::BANK_TRANSFER_DKK, 'DKK'],
    [PaymentForm::BANK_TRANSFER_GBP, 'GBP'],
    [PaymentForm::BANK_TRANSFER_NOK, 'NOK'],
    [PaymentForm::BANK_TRANSFER_SEK, 'SEK'],
]);

it('returns empty currency for generic payment forms', function () {
    expect(PaymentForm::BANK_TRANSFER->currency())->toBe('')
        ->and(PaymentForm::PREPAYMENT->currency())->toBe('');
});

it('returns correct payment forms for Poland', function () {
    $forms = PaymentForm::for(Country::POLAND);

    expect($forms)
        ->toBeArray()
        ->toHaveCount(7)
        ->toContain(PaymentForm::CASH_PLN)
        ->toContain(PaymentForm::BANK_TRANSFER_PLN);
});

it('returns correct payment forms for Sweden', function () {
    $forms = PaymentForm::for(Country::SWEDEN);

    expect($forms)
        ->toBeArray()
        ->toHaveCount(3)
        ->toContain(PaymentForm::BANK_TRANSFER_SEK);
});

it('returns generic payment forms for unsupported countries', function () {
    $forms = PaymentForm::for(Country::JAPAN);

    expect($forms)
        ->toBeArray()
        ->toHaveCount(2)
        ->toContain(PaymentForm::PREPAYMENT)
        ->toContain(PaymentForm::BANK_TRANSFER);
});

it('returns correct payment forms per country', function (Country $country, array $expected) {
    expect(PaymentForm::for($country))->toBe($expected);
})->with([
    'Norway' => [Country::NORWAY, [
        PaymentForm::PREPAYMENT_NOK_100,
        PaymentForm::PREPAYMENT_NOK_50,
        PaymentForm::BANK_TRANSFER_NOK,
    ]],
    'United Kingdom' => [Country::UNITED_KINGDOM, [
        PaymentForm::PREPAYMENT_GBP_100,
        PaymentForm::PREPAYMENT_GBP_50,
        PaymentForm::BANK_TRANSFER_GBP,
    ]],
    'Denmark' => [Country::DENMARK, [
        PaymentForm::PREPAYMENT_DKK_100,
        PaymentForm::PREPAYMENT_DKK_50,
        PaymentForm::BANK_TRANSFER_DKK,
    ]],
]);

it('can return list of payments as array', function () {
    $data = PaymentForm::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(25)
        ->each->toHaveKeys(['value', 'name', 'currency']);
});

it('includes bank transfer PLN with correct data in toArray()', function () {
    expect(PaymentForm::toArray())->toContain([
        'name' => PaymentForm::BANK_TRANSFER_PLN->description(),
        'currency' => PaymentForm::BANK_TRANSFER_PLN->currency(),
        'value' => PaymentForm::BANK_TRANSFER_PLN->value,
    ]);
});
