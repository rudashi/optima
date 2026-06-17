<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Models\EmployeeTest;

use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Tests\TestCase;
use TypeError;

uses(TestCase::class);

mutates(Employee::class);

it('maps all fields from the database row', function () {
    $employee = Employee::make(fakeEmployeeRow([
        'id'              => '5',
        'code'            => '001E',
        'firstname'       => 'Jan',
        'lastname'        => 'Kowalski',
        'email'           => 'jan.kowalski@example.com',
        'job_title'       => 'Operator',
        'department_id'   => '3',
        'department_name' => 'DRUK',
        'company'         => 'ACME',
        'rcp'             => '123',
        'deleted'         => 0,
    ]));

    expect($employee)
        ->id->toBe(5)
        ->code->toBe('001E')
        ->firstname->toBe('Jan')
        ->lastname->toBe('Kowalski')
        ->email->toBe('jan.kowalski@example.com')
        ->job_title->toBe('Operator')
        ->department_id->toBe(3)
        ->department_name->toBe('DRUK')
        ->company->toBe('ACME')
        ->rcp->toBe('123')
        ->deleted->toBeFalse();
});

it('casts deleted to a boolean', function (int $deleted, bool $expected) {
    expect(Employee::make(fakeEmployeeRow(['deleted' => $deleted]))->deleted)->toBe($expected);
})->with([
    'truthy' => [1, true],
    'falsy'  => [0, false],
]);

it('returns null for a missing job title and rcp', function () {
    $employee = Employee::make(fakeEmployeeRow(['job_title' => null, 'rcp' => null]));

    expect($employee->job_title)->toBeNull()
        ->and($employee->rcp)->toBeNull();
});

it('casts a numeric rcp to a string', function () {
    expect(Employee::make(fakeEmployeeRow(['rcp' => 123]))->rcp)->toBe('123');
});

it('rejects a row without an email', function () {
    expect(fn () => Employee::make(fakeEmployeeRow(['email' => null])))
        ->toThrow(TypeError::class);
});
