<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Rudashi\Optima\OptimaServiceProvider;
use Rudashi\Optima\Services\OptimaService;

class TestCase extends BaseTestCase
{
    public OptimaService $service;

    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');

        $this->service = app(OptimaService::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            OptimaServiceProvider::class,
        ];
    }

    // Testbench boots from its bundled skeleton in `vendor/` unless we repoint the base
    // path. Embedded in the monorepo we point it at the Composer root so Testbench reads
    // that root's `.env` (the `optima` connection's `MS_*`). Standalone (this package is
    // the root) we keep the skeleton — its own `.env` is irrelevant and it ships a
    // writable `bootstrap/cache` the bare package dir lacks.
    protected function getApplicationBasePath()
    {
        if (is_file(\Composer\InstalledVersions::getRootPackage()['install_path'] . '.env')) {
            return \Composer\InstalledVersions::getRootPackage()['install_path'];
        }

        return parent::getApplicationBasePath();
    }

    // config/database.php always ships the real sqlsrv `optima` connection — it's package
    // config, read by real consumers too, so it must stay test-agnostic. Test-only: when no
    // MS_HOST is present (no real MSSQL pointed at — neither staging nor Docker), fall back
    // to an in-memory sqlite connection instead.
    //
    // Runs in defineEnvironment() because Testbench calls it right after config files are
    // loaded (env('MS_HOST') in config/database.php has already resolved) and before
    // BootProviders — connections resolve lazily, so this mutation is guaranteed visible to
    // any later DB::connection('optima') call.
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        if (! env('MS_HOST')) {
            $app['config']->set('database.connections.optima', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]);
        }
    }
}
