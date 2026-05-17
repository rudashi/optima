<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HealthCheck\HealthCheckControllerTest;

use Rudashi\Optima\Services\DatabaseHealthCheckService;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('return response 500 with error message', function () {
    config(['database.connections.optima.driver' => 'sqlite']);

    $this->get(route('api.optima.ping'))
        ->assertStatus(500)
        ->assertJson([
            'status' => DatabaseHealthCheckService::PROBLEM,
            'message' => 'Could not connect to db',
        ]);
});
