<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Services\OptimaServiceTest;

use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('can check connection', function () {
    expect(optima(false)->hasConnection())
        ->toBeTrue();
});
