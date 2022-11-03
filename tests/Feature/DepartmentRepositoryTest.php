<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\DepartmentRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\Repositories\DepartmentRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

function repository(): DepartmentRepository
{
    return app(DepartmentRepository::class);
}

$departments = [
    'DRUK',
    'INTROLIGATORNIA',
    'PREPRESS',
    'BIURO',
];

it('can get all departments', function() {

    $data = repository()->all();

    expect($data)
        ->toBeInstanceOf(Collection::class)
        ->each(function($item) {
            $item->toBeInstanceOf(Department::class)
                ->toHaveProperties([
                    'id',
                    'name',
                    'parent_id',
                    'user_code',
                ]);
        });
});

it('can find a department by code', function (string $code) {

    $data = repository()->findByCode($code);

    expect($data)
        ->toBeInstanceOf(Department::class)
        ->toHaveProperty('name', $code)
        ->not()->toHaveProperty('parent_id', null)
        ->not()->toHaveProperty('user_code', null)
        ->toHaveProperties([
            'id',
            'name',
            'parent_id',
            'user_code',
        ]);

})->with($departments);

it('can find a department using alias method `find`', function (string $code) {

    $data = repository()->find($code);

    expect($data)
        ->toBeInstanceOf(Department::class)
        ->toHaveProperty('name', $code);

})->with($departments);

it('throws an exception when department is archived', function (string $code) {

    $this->expectExceptionMessage(__('Given code :code is invalid or not in the OPTIMA.', ['code' => $code]));

    repository()->findByCode($code);

})->with(['KIEROWNICTWO PRODUKCJI'])->throws(RecordsNotFoundException::class);
