<?php

declare(strict_types=1);

use Rudashi\Optima\Tests\Support\FakeDTO;

function fakeCustomerRow(array $override = []): stdClass
{
    return (object) array_merge([
        'id'              => fake()->numberBetween(1, 9999),
        'code'            => fake()->lexify('????'),
        'company'         => fake()->company(),
        'name_line_two'   => null,
        'name_line_three' => null,
        'country'         => null,
        'city'            => null,
        'postal_code'     => null,
        'street'          => null,
        'building_number' => null,
        'suite_number'    => null,
        'nip'             => null,
        'deleted'         => 0,
    ], $override);
}

function fakeDepartmentRow(array $override = []): stdClass
{
    return (object) array_merge([
        'id'        => fake()->numberBetween(1, 999),
        'name'      => strtoupper(fake()->word()),
        'user_code' => fake()->lexify('???'),
        'parent_id' => fake()->numberBetween(1, 20),
    ], $override);
}

function fakeEmployeeRow(array $override = []): stdClass
{
    return (object) array_merge([
        'id'              => fake()->numberBetween(1, 9999),
        'code'            => fake()->lexify('???E'),
        'firstname'       => fake()->firstName(),
        'lastname'        => fake()->lastName(),
        'email'           => fake()->email(),
        'job_title'       => fake()->jobTitle(),
        'department_id'   => fake()->numberBetween(1, 50),
        'department_name' => strtoupper(fake()->word()),
        'company'         => fake()->company(),
        'rcp'             => null,
        'deleted'         => 0,
    ], $override);
}

function fakeCollectionArray(string $type = 'object', int $total = 3): array
{
    return array_map(function () use ($type) {
        $array = [
            'id'     => fake()->numberBetween(),
            'uuid'   => fake()->uuid(),
            'email'  => fake()->email(),
            'active' => 1,
        ];
        settype($array, $type);

        return $array;
    }, range(0, $total - 1));
}

function dto(string $classname = FakeDTO::class): FakeDTO
{
    return new $classname(
        id: fake()->numberBetween(1, 10),
        order_id: fake()->numberBetween(11, 100),
        name: fake()->name(),
        description: fake()->company(),
    );
}
