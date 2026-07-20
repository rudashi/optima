<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\CountryTest;

use Rudashi\Optima\Enums\Country;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('can return list of Countries', function () {
    $data = Country::toArray();

    expect($data)
        ->toBeArray()
        ->toHaveCount(42)
        ->toContain([
            'code' => Country::POLAND->value,
            'name' => Country::POLAND->description(),
            'currency' => Country::POLAND->currency(),
        ])
        ->each->toHaveKeys(['code', 'name', 'currency']);
});
