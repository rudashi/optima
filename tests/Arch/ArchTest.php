<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

use Rudashi\Optima\OptimaServiceProvider;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;

arch('no debug')
    ->expect('Rudashi\Optima')
    ->not->toUse(['die', 'dd', 'dump', 'var_dump']);

arch('no env()')
    ->expect('Rudashi\Optima')
    ->not()->toUse('env');

arch('strict types')
    ->expect('Rudashi\Optima')
    ->toUseStrictTypes();

arch('strict equality')
    ->expect('Rudashi\Optima')
    ->toUseStrictEquality();
