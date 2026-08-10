<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Services\OptimaServiceTest;

it('can check connection', function () {
    expect(optima(false)->hasConnection())
        ->toBeTrue();
});
