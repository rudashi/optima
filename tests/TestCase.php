<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

use Rudashi\Optima\Services\OptimaService;

class TestCase extends \Tests\TestCase
{

    public OptimaService $db;

    protected function setUp(): void
    {
        parent::setUp();

        $this->db = app(OptimaService::class);
    }

}
