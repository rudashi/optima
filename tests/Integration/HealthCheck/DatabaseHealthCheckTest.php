<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\HealthCheck\DatabaseHealthCheckTest;

use Rudashi\Optima\Services\DatabaseHealthCheckService;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('can connect to optima database', function () {
    $service = new DatabaseHealthCheckService(app('db'));

    expect($service->status())
        ->toBeArray()
        ->toHaveKeys([
            'status',
            'context',
        ])
        ->toHaveKey('status', $service::OK)
        ->toHaveKey('context', []);
});
