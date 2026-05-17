<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Rudashi\Optima\OptimaServiceProvider;
use Rudashi\Optima\Services\OptimaService;

class TestCase extends BaseTestCase
{
    public OptimaService $service;

    protected function getPackageProviders($app): array
    {
        return [OptimaServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'optima');
        $app['config']->set('database.connections.optima', [
            'driver'   => 'sqlsrv',
            'host'     => env('OPTIMA_DB_HOST', '127.0.0.1'),
            'port'     => env('OPTIMA_DB_PORT', 1433),
            'database' => env('OPTIMA_DB_DATABASE', 'optima_test'),
            'username' => env('OPTIMA_DB_USERNAME', 'sa'),
            'password' => env('OPTIMA_DB_PASSWORD', ''),
            'encrypt'  => 'no',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(OptimaService::class);
    }
}
