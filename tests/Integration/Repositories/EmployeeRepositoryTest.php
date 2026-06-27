<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Repositories\EmployeeRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('maps an employee found by code to a fully typed model', function (string $code) {
    expect(resolve(EmployeeRepository::class)->findByCode($code))
        ->toBeInstanceOf(Employee::class)
        ->id->toBeInt()
        ->code->toBe($code)
        ->firstname->toBeString()
        ->lastname->toBeString()
        ->email->toBeString()
        ->job_title->toBeNullableString()
        ->department_id->toBeInt()
        ->department_name->toBeString()
        ->company->toBeString()
        ->rcp->toBeNullableString()
        ->deleted->toBeBool();
})->with([
    '130E',
    '012E',
    '019E',
    '023E',
    '074E_DOT_1_2_1CBR',
]);

it('find() is an alias for findByCode()', function () {
    expect(resolve(EmployeeRepository::class)->find('023E'))
        ->toBeInstanceOf(Employee::class)
        ->code->toBe('023E');
});

it('throws when the employee code is not found', function () {
    expect(fn () => resolve(EmployeeRepository::class)->findByCode(''))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given acronym :code is invalid or not in the OPTIMA.', ['code' => '']),
        );
});

it('throws when the employee is archived', function () {
    expect(fn () => resolve(EmployeeRepository::class)->findByCode('XXX'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Employee with given acronym :code is archived.', ['code' => 'XXX']),
        );
});
