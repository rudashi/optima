<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CountryTest;

use Rudashi\Optima\Contracts\Arrayable;
use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\Country;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Country::class);

it('implements Arrayable and Describable contracts', function () {
    expect(Country::class)
        ->toImplement(Arrayable::class)
        ->toImplement(Describable::class);
});

it('has correct backed values for key countries', function (Country $case, string $value) {
    expect($case->value)->toBe($value);
})->with([
    [Country::POLAND, 'PL'],
    [Country::GERMANY, 'DE'],
    [Country::FRANCE, 'FR'],
    [Country::UNITED_KINGDOM, 'GB'],
    [Country::UNITED_STATES, 'US'],
    [Country::NULL, ''],
]);

it('resolves to NULL case for empty or missing name via of()', function () {
    expect(Country::of())->toBe(Country::NULL)
        ->and(Country::of(''))->toBe(Country::NULL)
        ->and(Country::of('NonExistentCountry'))->toBe(Country::NULL);
});

it('resolves correct case via of() using description', function () {
    expect(Country::of('Poland'))->toBe(Country::POLAND)
        ->and(Country::of('Germany'))->toBe(Country::GERMANY);
});

it('returns correct currency for each country', function () {
    $currencies = [
        'POLAND' => 'PLN',
        'BELGIUM' => 'EUR',
        'BULGARIA' => 'BGN',
        'SAINT_BARTHELEMY' => 'EUR',
        'SWITZERLAND' => 'CHF',
        'CZECH_REPUBLIC' => 'CZK',
        'GERMANY' => 'EUR',
        'DENMARK' => 'DKK',
        'ESTONIA' => 'EUR',
        'SPAIN' => 'EUR',
        'FINLAND' => 'EUR',
        'FRANCE' => 'EUR',
        'UNITED_KINGDOM' => 'GBP',
        'IRELAND' => 'EUR',
        'LUXEMBOURG' => 'EUR',
        'LATVIA' => 'EUR',
        'NETHERLANDS' => 'EUR',
        'NORWAY' => 'NOK',
        'PORTUGAL' => 'EUR',
        'SWEDEN' => 'SEK',
        'UNITED_STATES' => 'USD',
        'AUSTRIA' => 'EUR',
        'GREECE' => 'EUR',
        'ICELAND' => 'ISK',
        'ITALY' => 'EUR',
        'LITHUANIA' => 'EUR',
        'SLOVAKIA' => 'EUR',
        'SLOVENIA' => 'EUR',
        'UKRAINE' => 'UAH',
        'JAPAN' => 'JPY',
        'HUNGARY' => 'EUR',
        'AUSTRALIA' => 'AUD',
        'CANADA' => 'CAD',
        'ISRAEL' => 'ILS',
        'CHINA' => 'CNY',
        'VIRGIN_ISLANDS_BRITISH' => 'USD',
        'QATAR' => 'QAR',
        'ROMANIA' => 'RON',
        'PAKISTAN' => 'PKR',
        'NULL' => '',
    ];

    expect($currencies)->toHaveCount(count(Country::cases()));

    foreach (Country::cases() as $case) {
        expect($case->currency())->toBe($currencies[$case->name]);
    }
});

it('returns a non-empty description for all non-null cases', function () {
    $cases = array_filter(Country::cases(), fn ($c) => $c !== Country::NULL);

    foreach ($cases as $case) {
        expect($case->description())->toBeString()->not->toBeEmpty();
    }
});

it('returns empty string description for NULL case', function () {
    expect(Country::NULL->description())->toBe('');
});

it('can return list of countries as array', function () {
    $data = Country::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(39)
        ->each->toHaveKeys(['code', 'name', 'currency']);
});

it('excludes NULL case from toArray()', function () {
    $values = array_column(Country::toArray(), 'code');

    expect($values)->not->toContain('');
});

it('includes Poland with correct data in toArray()', function () {
    expect(Country::toArray())->toContain([
        'code' => Country::POLAND->value,
        'name' => Country::POLAND->description(),
        'currency' => Country::POLAND->currency(),
    ]);
});
