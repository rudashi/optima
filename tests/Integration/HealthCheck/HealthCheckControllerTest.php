<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\HealthCheck\HealthCheckControllerTest;

use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('can ping optima database and get response 200', function () {
    $this->get(route('api.optima.ping'))
        ->assertOk()
        ->assertJson([
            'message' => 'pong',
        ]);
});
