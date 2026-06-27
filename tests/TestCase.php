<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Rudashi\Optima\OptimaServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [OptimaServiceProvider::class];
    }
}
