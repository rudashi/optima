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

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'optima');
        $app['config']->set('database.connections.optima', [
            'driver'   => 'sqlsrv',
            'host'     => env('MS_HOST', 'localhost'),
            'port'     => env('MS_PORT', 1433),
            'database' => env('MS_DATABASE', 'optima_test'),
            'read'     => [
                'username' => env('MS_USERNAME', 'sa'),
                'password' => env('MS_PASSWORD', 'Optima!2026'),
            ],
            'write'    => [
                'username' => env('MS_SUDO_USERNAME', env('MS_USERNAME', 'sa')),
                'password' => env('MS_SUDO_PASSWORD', env('MS_PASSWORD', 'Optima!2026')),
            ],
            'charset'                  => 'utf8mb4',
            'collation'                => 'utf8mb4_unicode_ci',
            'trust_server_certificate' => true,
            'encrypt'                  => 'no',
        ]);
    }
}
