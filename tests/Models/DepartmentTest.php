<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Models\DepartmentTest;

use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(Department::class);

it('maps all fields from the database row', function () {
    $department = Department::make(fakeDepartmentRow([
        'id'        => '7',
        'name'      => 'FINANSE',
        'user_code' => 'FIN',
        'parent_id' => '3',
    ]));

    expect($department)
        ->id->toBe(7)
        ->name->toBe('FINANSE')
        ->user_code->toBe('FIN')
        ->parent_id->toBe(3);
});

it('returns null when the parent is missing', function () {
    expect(Department::make(fakeDepartmentRow(['parent_id' => null]))->parent_id)->toBeNull();
});

it('converts to an array', function () {
    $department = Department::make(fakeDepartmentRow([
        'id'        => 7,
        'name'      => 'FINANSE',
        'user_code' => 'FIN',
        'parent_id' => 3,
    ]));

    expect($department->toArray())->toBe([
        'id'        => 7,
        'name'      => 'FINANSE',
        'user_code' => 'FIN',
        'parent_id' => 3,
    ]);
});
