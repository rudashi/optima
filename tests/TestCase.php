<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

use Rudashi\Optima\Services\OptimaService;
use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public OptimaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(OptimaService::class);
    }
}
