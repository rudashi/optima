<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HealthCheck\HealthCheckControllerTest;

use Rudashi\Optima\Services\DatabaseHealthCheckService;
use Tests\TestCase;

uses(TestCase::class);

it('can ping optima database and get response 200', function () {
    $this->get(route('api.optima.ping'))
        ->assertOk()
        ->assertJson([
            'message' => 'pong',
        ]);
});

it('return response 500 with error message', function () {
    config(['database.connections.optima.driver' => 'sqlite']);

    $this->get(route('api.optima.ping'))
        ->assertStatus(500)
        ->assertJson([
            'status' => DatabaseHealthCheckService::PROBLEM,
            'message' => 'Could not connect to db',
        ]);
});
