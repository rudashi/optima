<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CountryTest;

use Rudashi\Optima\Contracts\Arrayable;
use Rudashi\Optima\Contracts\Describable;
use Rudashi\Optima\Enums\Country;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Country::class);

it('is a string-backed enum', function () {
    expect(Country::POLAND)->toBeInstanceOf(Country::class)
        ->and(Country::POLAND->value)->toBeString();
});

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

it('can be created from a string value', function () {
    expect(Country::from('PL'))->toBe(Country::POLAND)
        ->and(Country::from('DE'))->toBe(Country::GERMANY);
});

it('returns null from tryFrom for unknown value', function () {
    expect(Country::tryFrom('XX'))->toBeNull()
        ->and(Country::tryFrom(''))->toBe(Country::NULL);
});

it('resolves to NULL case for empty or missing name via of()', function () {
    expect(Country::of())->toBe(Country::NULL)
        ->and(Country::of(''))->toBe(Country::NULL)
        ->and(Country::of('NonExistentCountry'))->toBe(Country::NULL);
});

it('resolves correct case via of() using description', function () {
    expect(Country::of('Poland'))->toBe(Country::POLAND)
        ->and(Country::of('Germany'))->toBe(Country::GERMANY);
});

it('returns correct currency for each country', function (Country $case, string $currency) {
    expect($case->currency())->toBe($currency);
})->with([
    [Country::POLAND, 'PLN'],
    [Country::GERMANY, 'EUR'],
    [Country::UNITED_KINGDOM, 'GBP'],
    [Country::UNITED_STATES, 'USD'],
    [Country::NORWAY, 'NOK'],
    [Country::SWEDEN, 'SEK'],
    [Country::DENMARK, 'DKK'],
    [Country::SWITZERLAND, 'CHF'],
    [Country::NULL, ''],
]);

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
