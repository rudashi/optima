<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Schema\RepositoryQueryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Services\Repositories\DepartmentRepository;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

beforeEach(function () {
    $service = app(OptimaService::class);

    $this->customers = new CustomerRepository($service);
    $this->employees = new EmployeeRepository($service);
    $this->departments = new DepartmentRepository($service);
});

it('executes the canonical customer SELECT against the live schema', function () {
    expect(fn () => $this->customers->findByCode('__OPTIMA_SMOKE_PROBE__'))
        ->toThrow(RecordsNotFoundException::class);
});

it('executes the canonical employee SELECT against the live schema', function () {
    expect(fn () => $this->employees->findByCode('__OPTIMA_SMOKE_PROBE__'))
        ->toThrow(RecordsNotFoundException::class);
});

it('executes the canonical department join query against the live schema', function () {
    expect($this->departments->all())
        ->toBeInstanceOf(Collection::class);
});
