<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Repositories\DepartmentRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\Repositories\DepartmentRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('returns a typed Collection of departments from all()', function () {
    $departments = resolve(DepartmentRepository::class)->all();

    expect($departments->isEmpty())->toBeFalse()
        ->and($departments)
        ->toBeInstanceOf(Collection::class)
        ->each->toBeInstanceOf(Department::class)
        ->and($departments->first())
        ->id->toBeInt()
        ->name->toBeString()
        ->user_code->toBeString()
        ->parent_id->toBeNullableInt();
});

it('maps a department found by code to a typed model', function (string $code) {
    expect(resolve(DepartmentRepository::class)->findByCode($code))
        ->toBeInstanceOf(Department::class)
        ->id->toBeInt()
        ->name->toBeString()
        ->user_code->toBeString()
        ->parent_id->toBeNullableInt();
})->with([
    'DRUK',
    'INTROLIGATORNIA',
    'PREPRESS',
    'BIURO',
]);

it('uppercases the code before querying', function () {
    expect(resolve(DepartmentRepository::class)->findByCode('druk'))
        ->toBeInstanceOf(Department::class);
});

it('find() is an alias for findByCode()', function () {
    expect(resolve(DepartmentRepository::class)->find('DRUK'))
        ->toBeInstanceOf(Department::class);
});

it('throws when the department code is not found', function () {
    expect(fn () => resolve(DepartmentRepository::class)->findByCode('__MISSING_DEPARTMENT__'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => '__MISSING_DEPARTMENT__']),
        );
});
