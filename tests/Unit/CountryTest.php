<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Unit\CountryTest;

use Rudashi\Optima\Enums\Country;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('can return list of Countries', function () {
    $data = Country::toSelect();

    expect($data)
        ->toBeArray()
        ->toBeGreaterThanOrEqual(38)
        ->toContain([
            'code' => Country::POLAND->value,
            'name' => Country::POLAND->description(),
        ])
        ->not->toContain([
            'code' => Country::GERMANY->value,
            'name' => Country::POLAND->description(),
        ]);
});
