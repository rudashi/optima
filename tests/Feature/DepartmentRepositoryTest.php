<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\DepartmentRepositoryTest;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\Repositories\DepartmentRepository;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

dataset('departments', [
    'DRUK',
    'INTROLIGATORNIA',
    'PREPRESS',
    'BIURO',
]);

beforeEach(function () {
    $this->repository = resolve(DepartmentRepository::class);
});

it('can get all departments', function () {
    $data = $this->repository->all();

    expect($data)
        ->toBeInstanceOf(Collection::class)
        ->each(function ($item) {
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
    $data = $this->repository->findByCode($code);

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
})->with('departments');

it('can find a department using alias method `find`', function (string $code) {
    $data = $this->repository->find($code);

    expect($data)
        ->toBeInstanceOf(Department::class)
        ->toHaveProperty('name', $code);
})->with('departments');

it('throws an exception when department is archived', function (string $code) {
    expect(fn () => $this->repository->findByCode($code))
        ->toThrow(
            exception: RecordsNotFoundException::class,
            exceptionMessage: __('Given code :code is invalid or not in the OPTIMA.', ['code' => $code]),
        );
})->with(['KIEROWNICTWO PRODUKCJI']);
