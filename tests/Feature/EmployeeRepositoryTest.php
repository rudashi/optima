<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\EmployeeRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

function repository(): EmployeeRepository
{
    return app(EmployeeRepository::class);
}

$employee = [
    '023E',
    [
        'firstname' => 'BORYS',
        'job_title' => 'STARSZY INŻYNIER OPROGRAMOWANIA',
        'department_id' => '37',
        'deleted' => false,
    ],
];

$employees = [
    '130E',
    '012E',
    '019E',
    '023E',
    '074E_DOT_1_2_1CBR',
];

it('can find an employee by code', function (string $code, array $dataset) {
    $data = repository()->findByCode($code);

    expect($data)
        ->toBeInstanceOf(Employee::class)
        ->toHaveProperty('code', $code)
        ->toHaveProperty('firstname', $dataset['firstname'])
        ->toHaveProperty('job_title', $dataset['job_title'])
        ->toHaveProperty('department_id', $dataset['department_id'])
        ->toHaveProperty('deleted', $dataset['deleted'])
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
})->with([$employee]);

it('can find an employee using alias method `find`', function (string $code) {
    $data = repository()->find($code);

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
})->with($employees);

it('throws an exception when employee code not exists', function () {
    expect(fn () => repository()->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given acronym :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('throws an exception when employee is archived', function (string $code) {
    expect(fn () => repository()->findByCode($code))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Employee with given acronym :code is archived.', ['code' => $code]),
        );
})->with(['XXX']);
