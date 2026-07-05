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

    // Testbench boots from its bundled skeleton in `vendor/` unless we repoint the base
    // path. Embedded in a monorepo we point it at the Composer root so Testbench reads that
    // root's `.env` (the `optima` connection's `MS_*`). Standalone (this package is the
    // root) we keep the skeleton — its own `.env` is irrelevant and it ships a writable
    // `bootstrap/cache`, which the bare package dir does not.
    protected function getApplicationBasePath()
    {
        if (is_file((\Composer\InstalledVersions::getRootPackage()['install_path']) . '.env')) {
            return \Composer\InstalledVersions::getRootPackage()['install_path'];
        }

        return parent::getApplicationBasePath();
    }
}
