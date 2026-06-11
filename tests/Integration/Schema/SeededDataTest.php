<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Schema\SeededDataTest;

use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Support\Facades\DB;
use Rudashi\Optima\Enums\CustomerGroup;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\Repositories\CustomerRepository;
use Rudashi\Optima\Services\Repositories\DepartmentRepository;
use Rudashi\Optima\Services\Repositories\EmployeeRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('fixtures');

it('finds a customer by code from seeded fixture', function () {
    $result = DB::connection('optima')
        ->table('CDN.Kontrahenci')
        ->where('Knt_Kod', 'TEST-A')
        ->where('Knt_Nieaktywny', 0)
        ->first();

    expect($result)
        ->not->toBeNull()
        ->and($result->Knt_Nazwa1)->toBe('Test Company A');
});

it('maps a fully populated customer through the repository', function () {
    $repository = new CustomerRepository(app(OptimaService::class));

    expect($repository->findByCode('TEST-FULL'))
        ->toBeInstanceOf(Customer::class)
        ->id->toBe(4)
        ->code->toBe('TEST-FULL')
        ->company->toBe('Test Company Full')
        ->name_line_two->toBe('Sp. z o.o.')
        ->name_line_three->toBe('Oddział')
        ->name->toBe('Test Company Full Sp. z o.o. Oddział')
        ->country->toBe('Polska')
        ->city->toBe('Gdańsk')
        ->postal_code->toBe('82-500')
        ->street->toBe('ul. Polna')
        ->building_number->toBe('26')
        ->suite_number->toBe('1')
        ->nip->toBe('5860001234')
        ->deleted->toBeFalse();
});

it('applies the group filter when finding a customer by code', function () {
    $repository = new CustomerRepository(app(OptimaService::class));

    expect($repository->findByCode('TEST-FULL', CustomerGroup::SUBCONTRACTOR->value))
        ->code->toBe('TEST-FULL')
        ->and(fn () => $repository->findByCode('TEST-FULL', CustomerGroup::SUPPLIER->value))
        ->toThrow(RecordsNotFoundException::class);

});

it('returns an inactive customer flagged as deleted', function () {
    $repository = new CustomerRepository(app(OptimaService::class));

    expect($repository->findByCode('INACTIVE'))
        ->toBeInstanceOf(Customer::class)
        ->code->toBe('INACTIVE')
        ->company->toBe('Inactive Company')
        ->deleted->toBeTrue();
});

it('maps a customer with only the required fields populated', function () {
    $repository = new CustomerRepository(app(OptimaService::class));

    expect($repository->findByCode('TEST-B'))
        ->toBeInstanceOf(Customer::class)
        ->code->toBe('TEST-B')
        ->company->toBe('Test Company B')
        ->name->toBe('Test Company B')
        ->name_line_two->toBeNull()
        ->name_line_three->toBeNull()
        ->country->toBeNull()
        ->city->toBeNull()
        ->postal_code->toBeNull()
        ->street->toBeNull()
        ->building_number->toBeNull()
        ->suite_number->toBeNull()
        ->nip->toBeNull()
        ->deleted->toBeFalse();
});

it('maps a fully populated employee through the repository', function () {
    $repository = new EmployeeRepository(app(OptimaService::class));

    expect($repository->findByCode('001E'))
        ->toBeInstanceOf(Employee::class)
        ->id->toBe(1)
        ->code->toBe('001E')
        ->firstname->toBe('Jan')
        ->lastname->toBe('Kowalski')
        ->email->toBe('jan.kowalski.new@example.com')
        ->job_title->toBe('Kierownik')
        ->department_id->toBe(2)
        ->department_name->toBe('WYDA')
        ->company->toBe('TOTEM')
        ->rcp->toBe('RCP-001')
        ->deleted->toBeFalse();
});

it('selects the latest employment record by max PRE_PreId', function () {
    $repository = new EmployeeRepository(app(OptimaService::class));

    expect($repository->findByCode('001E'))
        ->email->toBe('jan.kowalski.new@example.com')
        ->job_title->toBe('Kierownik');
});

it('joins the active RCP card and ignores expired ones', function () {
    $repository = new EmployeeRepository(app(OptimaService::class));

    expect($repository->findByCode('001E'))->rcp->toBe('RCP-001');
    expect($repository->findByCode('002E'))->rcp->toBeNull();
});

it('throws when the employee is archived', function () {
    $repository = new EmployeeRepository(app(OptimaService::class));

    expect(fn () => $repository->findByCode('003E'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Employee with given acronym :code is archived.', ['code' => '003E']),
        );
});

it('lists seeded departments through the repository', function () {
    $repository = new DepartmentRepository(app(OptimaService::class));

    $departments = $repository->all();

    expect($departments)
        ->toBeInstanceOf(Collection::class)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(Department::class)
        ->and($departments->first())
        ->id->toBe(2)
        ->name->toBe('WYDZIAŁ A')
        ->user_code->toBe('001E')
        ->parent_id->toBe(1);

});

it('excludes departments with an empty name', function () {
    $repository = new DepartmentRepository(app(OptimaService::class));

    expect($repository->all()->pluck('id')->all())
        ->toBe([2, 3]);
});

it('maps a department found by code through the repository', function () {
    $repository = new DepartmentRepository(app(OptimaService::class));

    expect($repository->findByCode('WYDA'))
        ->toBeInstanceOf(Department::class)
        ->id->toBe(2)
        ->name->toBe('WYDZIAŁ A')
        ->user_code->toBe('001E')
        ->parent_id->toBe(1);
});
