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

it('implements Arrayable and Describable contracts', function () {
    expect(PaymentForm::class)
        ->toImplement(Arrayable::class)
        ->toImplement(Describable::class);
});

it('has correct backed values', function () {
    $values = [
        'CASH_PLN' => 1,
        'PREPAYMENT_PLN' => 19,
        'PREPAYMENT_PLN_100' => 62,
        'PREPAYMENT_PLN_50' => 63,
        'PREPAYMENT' => 46,
        'PREPAYMENT_DKK_100' => 66,
        'PREPAYMENT_DKK_50' => 70,
        'PREPAYMENT_EUR_100_E' => 64,
        'PREPAYMENT_EUR_50_E' => 65,
        'PREPAYMENT_GBP_100' => 69,
        'PREPAYMENT_GBP_50' => 71,
        'PREPAYMENT_NOK_100' => 68,
        'PREPAYMENT_NOK_50' => 72,
        'PREPAYMENT_SEK_100' => 67,
        'PREPAYMENT_SEK_50' => 73,
        'BANK_TRANSFER' => 36,
        'BANK_TRANSFER_PLN' => 44,
        'BANK_TRANSFER_PLN_ING' => 60,
        'BANK_TRANSFER_PLN_ERSTE' => 61,
        'BANK_TRANSFER_DKK' => 56,
        'BANK_TRANSFER_EUR_E' => 55,
        'BANK_TRANSFER_EUR_I' => 53,
        'BANK_TRANSFER_GBP' => 57,
        'BANK_TRANSFER_NOK' => 59,
        'BANK_TRANSFER_SEK' => 58,
    ];

    expect($values)->toHaveCount(count(PaymentForm::cases()));

    foreach (PaymentForm::cases() as $case) {
        expect($case->value)->toBe($values[$case->name]);
    }
});

it('returns correct description for each case', function () {
    $descriptions = [
        'CASH_PLN' => 'gotówka',
        'PREPAYMENT_PLN' => 'przedpłata',
        'PREPAYMENT_PLN_100' => 'przedpłata PLN 100%',
        'PREPAYMENT_PLN_50' => 'przedpłata PLN 50%',
        'PREPAYMENT' => 'prepayment',
        'PREPAYMENT_DKK_100' => 'prepayment DKK 100%',
        'PREPAYMENT_DKK_50' => 'prepayment DKK 50%',
        'PREPAYMENT_EUR_100_E' => 'prepayment EUR 100%E',
        'PREPAYMENT_EUR_50_E' => 'prepayment EUR 50%-E',
        'PREPAYMENT_GBP_100' => 'prepayment GBP 100%',
        'PREPAYMENT_GBP_50' => 'prepayment GBP 50%',
        'PREPAYMENT_NOK_100' => 'prepayment NOK 100%',
        'PREPAYMENT_NOK_50' => 'prepayment NOK 50%',
        'PREPAYMENT_SEK_100' => 'prepayment SEK 100%',
        'PREPAYMENT_SEK_50' => 'prepayment SEK 50%',
        'BANK_TRANSFER' => 'bank transfer',
        'BANK_TRANSFER_PLN' => 'przelew bankowy',
        'BANK_TRANSFER_PLN_ING' => 'przelew PLN - ING',
        'BANK_TRANSFER_PLN_ERSTE' => 'przelew PLN - ERSTE',
        'BANK_TRANSFER_DKK' => 'bank transfer DKK',
        'BANK_TRANSFER_EUR_E' => 'bank transfer EUR-E',
        'BANK_TRANSFER_EUR_I' => 'bank transfer EUR-I',
        'BANK_TRANSFER_GBP' => 'bank transfer GBP',
        'BANK_TRANSFER_NOK' => 'bank transfer NOK',
        'BANK_TRANSFER_SEK' => 'bank transfer SEK',
    ];

    expect($descriptions)->toHaveCount(count(PaymentForm::cases()));

    foreach (PaymentForm::cases() as $case) {
        expect($case->description())->toBe($descriptions[$case->name]);
    }
});

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

it('returns empty currency for generic payment forms', function (PaymentForm $case) {
    expect($case->currency())->toBe('');
})->with([
    PaymentForm::BANK_TRANSFER,
    PaymentForm::PREPAYMENT,
]);

it('returns empty currency for EUR payment forms', function (PaymentForm $case) {
    expect($case->currency())->toBe('');
})->with([
    PaymentForm::BANK_TRANSFER_EUR_E,
    PaymentForm::BANK_TRANSFER_EUR_I,
    PaymentForm::PREPAYMENT_EUR_100_E,
    PaymentForm::PREPAYMENT_EUR_50_E,
]);

it('returns correct payment forms per country', function (Country $country, array $expected) {
    expect(PaymentForm::for($country))->toBe($expected);
})->with([
    'Poland' => [Country::POLAND, [
        PaymentForm::CASH_PLN,
        PaymentForm::PREPAYMENT_PLN,
        PaymentForm::PREPAYMENT_PLN_100,
        PaymentForm::PREPAYMENT_PLN_50,
        PaymentForm::BANK_TRANSFER_PLN,
        PaymentForm::BANK_TRANSFER_PLN_ING,
        PaymentForm::BANK_TRANSFER_PLN_ERSTE,
    ]],
    'Sweden' => [Country::SWEDEN, [
        PaymentForm::PREPAYMENT_SEK_100,
        PaymentForm::PREPAYMENT_SEK_50,
        PaymentForm::BANK_TRANSFER_SEK,
    ]],
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
    'Japan (default)' => [Country::JAPAN, [
        PaymentForm::PREPAYMENT,
        PaymentForm::BANK_TRANSFER,
    ]],
]);

it('can return list of payments as array', function () {
    $data = PaymentForm::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(count(PaymentForm::cases()))
        ->each->toHaveKeys(['value', 'name', 'currency']);
});

it('includes bank transfer PLN with correct data in toArray()', function () {
    expect(PaymentForm::toArray())->toContain([
        'name' => PaymentForm::BANK_TRANSFER_PLN->description(),
        'currency' => PaymentForm::BANK_TRANSFER_PLN->currency(),
        'value' => PaymentForm::BANK_TRANSFER_PLN->value,
    ]);
});
