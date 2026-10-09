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

beforeEach(fn () => skipUnlessMssql());

beforeEach(function (): void {
    $this->departments = resolve(DepartmentRepository::class);
});

it('returns a typed Collection of departments from all()', function (): void {
    $departments = $this->departments->all();

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

it('maps a department found by code to a typed model', function (string $code): void {
    expect($this->departments->findByCode($code))
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

it('uppercases the code before querying', function (): void {
    expect($this->departments->findByCode('druk'))
        ->toBeInstanceOf(Department::class);
});

it('find() is an alias for findByCode()', function (): void {
    expect($this->departments->find('DRUK'))
        ->toBeInstanceOf(Department::class);
});

it('throws when the department code is not found', function (): void {
    expect(fn (): Department => $this->departments->findByCode('__MISSING_DEPARTMENT__'))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => '__MISSING_DEPARTMENT__']),
        );
});
