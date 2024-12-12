<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\EmployeeRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->repository = resolve(EmployeeRepository::class);
});

it('can find an employee by code', function () {
    $data = $this->repository->findByCode('023E');

    expect($data)
        ->toBeInstanceOf(Employee::class)
        ->toHaveProperty('code', '023E')
        ->toHaveProperty('firstname', 'BORYS')
        ->toHaveProperty('job_title')
        ->toHaveProperty('department_id')
        ->toHaveProperty('deleted', false)
        ->toHaveProperties([
            'id',
            'code',
            'firstname',
            'lastname',
            'email',
            'job_title',
            'department_id',
            'department_name',
            'company',
            'rcp',
            'deleted',
        ]);
});

it('can find an employee using alias method `find`', function (string $code) {
    $data = $this->repository->find($code);

    expect($data)
        ->toBeInstanceOf(Employee::class)
        ->toHaveProperty('code', $code)
        ->toHaveProperties([
            'id',
            'code',
            'firstname',
            'lastname',
            'email',
            'job_title',
            'department_id',
            'department_name',
            'company',
            'rcp',
            'deleted',
        ]);
})->with([
    '130E',
    '012E',
    '019E',
    '023E',
    '074E_DOT_1_2_1CBR',
]);

it('throws an exception when employee code not exists', function () {
    expect(fn () => $this->repository->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given acronym :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('throws an exception when employee is archived', function () {
    expect(fn () => $this->repository->findByCode('XXX'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Employee with given acronym :code is archived.', ['code' => 'XXX']),
        );
});
