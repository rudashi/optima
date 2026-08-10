<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\HealthCheck\DatabaseHealthCheckTest;

use Rudashi\Optima\Services\DatabaseHealthCheckService;

it('can connect to optima database', function () {
    $service = new DatabaseHealthCheckService(app('db'));

    expect($service->status())
        ->toBeArray()
        ->toHaveKey('status', $service::OK)
        ->toHaveKey('context', []);
});
