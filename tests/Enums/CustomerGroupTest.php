<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CustomerGroupTest;

use Rudashi\Optima\Enums\CustomerGroup;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(CustomerGroup::class);

it('is a string-backed enum', function () {
    expect(CustomerGroup::TRADING_COMPANY)->toBeInstanceOf(CustomerGroup::class)
        ->and(CustomerGroup::TRADING_COMPANY->value)->toBeString();
});

it('has correct backed values', function (CustomerGroup $case, string $value) {
    expect($case->value)->toBe($value);
})->with([
    [CustomerGroup::TRADING_COMPANY, 'FIRMA HANDLOWA'],
    [CustomerGroup::HEALTHCARE, 'OPIEKA ZDROWOTNA'],
    [CustomerGroup::FOREIGN_COMPANY, 'ZAGRANICZNA FIRMA'],
    [CustomerGroup::PUBLISHING_HOUSE, 'WYDAWNICTWO'],
    [CustomerGroup::INDIVIDUAL, 'OSOBA FIZYCZNA'],
    [CustomerGroup::INSTITUTION, 'INSTYTUCJA'],
    [CustomerGroup::SERVICE_COMPANY, 'FIRMA USŁUGOWA'],
    [CustomerGroup::UNIVERSITY, 'WYŻSZA UCZELNIA'],
    [CustomerGroup::TRANSPORT, 'TRANSPORTOWA'],
    [CustomerGroup::SUPPLIER, 'DOSTAWCA'],
    [CustomerGroup::SUBCONTRACTOR, 'PODWYKONAWCA'],
]);

it('can be created from a string value', function () {
    expect(CustomerGroup::from('FIRMA HANDLOWA'))->toBe(CustomerGroup::TRADING_COMPANY)
        ->and(CustomerGroup::from('DOSTAWCA'))->toBe(CustomerGroup::SUPPLIER);
});

it('returns null from tryFrom for unknown value', function () {
    expect(CustomerGroup::tryFrom('NIEZNANA'))->toBeNull()
        ->and(CustomerGroup::tryFrom(''))->toBeNull();
});

it('has exactly eleven cases', function () {
    expect(CustomerGroup::cases())->toHaveCount(11);
});
