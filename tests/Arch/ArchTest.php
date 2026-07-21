<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests;

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

arch('enums are backed')
    ->expect('Rudashi\Optima\Enums')
    ->toBeEnums();

arch('contracts are interfaces')
    ->expect('Rudashi\Optima\Contracts')
    ->toBeInterfaces();

arch('controllers')
    ->expect('Rudashi\Optima\Controllers')
    ->toHaveSuffix('Controller')
    ->toExtend('Illuminate\Routing\Controller');

arch('exceptions')
    ->expect('Rudashi\Optima\Exceptions')
    ->toHaveSuffix('Exception')
    ->toImplement('Throwable');

arch('models are readonly')
    ->expect('Rudashi\Optima\Models')
    ->toBeReadonly();

arch('repositories')
    ->expect('Rudashi\Optima\Services\Repositories')
    ->toHaveSuffix('Repository');
