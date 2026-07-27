<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Services\RelationHasManyThroughBuilderTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\RelationHasManyThroughBuilder;
use Rudashi\Optima\Tests\Fixtures\FakeRelation;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(RelationHasManyThroughBuilder::class);

it('passes pivot local keys to the relation', function () {
    $relation = new FakeRelation();
    $this->app->instance(FakeRelation::class, $relation);

    $builder = new RelationHasManyThroughBuilder(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments');
    $models = new Collection([
        (object) [
            'id'        => 1,
            'contracts' => [
                (object) ['dept_id' => 100, 'employee_id' => 1],
                (object) ['dept_id' => 200, 'employee_id' => 1],
            ],
        ],
    ]);

    $builder->init($models);

    expect($relation->receivedKeys)->toBe([100, 200]);
});

it('attaches deduplicated owner keys to related items', function () {
    $related = (object) ['dept_id' => 100, 'name' => 'IT'];
    $this->app->instance(FakeRelation::class, new FakeRelation([$related]));

    $builder = new RelationHasManyThroughBuilder(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments');
    $models = new Collection([
        (object) [
            'id'        => 1,
            'contracts' => [
                (object) ['dept_id' => 100, 'employee_id' => 1],
                (object) ['dept_id' => 100, 'employee_id' => 1],
            ],
        ],
        (object) [
            'id'        => 2,
            'contracts' => [
                (object) ['dept_id' => 100, 'employee_id' => 2],
            ],
        ],
    ]);

    $builder->init($models);

    expect(array_values($related->employee_id))->toBe([1, 2]);
});

it('matches related models to owners through the pivot', function () {
    $this->app->instance(FakeRelation::class, new FakeRelation([
        (object) ['dept_id' => 100, 'name' => 'IT'],
        (object) ['dept_id' => 200, 'name' => 'HR'],
    ]));

    $builder = new RelationHasManyThroughBuilder(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments');
    $models = new Collection([
        (object) [
            'id'        => 1,
            'contracts' => [
                (object) ['dept_id' => 100, 'employee_id' => 1],
                (object) ['dept_id' => 200, 'employee_id' => 1],
            ],
        ],
        (object) [
            'id'        => 2,
            'contracts' => [
                (object) ['dept_id' => 100, 'employee_id' => 2],
            ],
        ],
    ]);

    $result = $builder->match($builder->init($models), $models);

    expect($result->first()->departments)->toHaveCount(2)
        ->and($result->first()->departments[0]->name)->toBe('IT')
        ->and($result->first()->departments[1]->name)->toBe('HR')
        ->and($result->last()->departments)->toHaveCount(1)
        ->and($result->last()->departments[0]->name)->toBe('IT');
});

it('keeps the default for owners without pivot rows', function () {
    $this->app->instance(FakeRelation::class, new FakeRelation());

    $builder = new RelationHasManyThroughBuilder(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments');
    $models = new Collection([(object) ['id' => 1, 'contracts' => []]]);

    $result = $builder->match($builder->init($models), $models);

    expect($result->first()->departments)->toBe([]);
});

it('does not match a single related object', function () {
    $this->app->instance(FakeRelation::class, new FakeRelation());

    $builder = new RelationHasManyThroughBuilder(FakeRelation::class, 'contracts', 'id', 'dept_id', 'dept_id', 'employee_id', 'departments');
    $models = new Collection([(object) ['id' => 1, 'contracts' => []]]);

    $builder->init($models);
    $result = $builder->match((object) ['dept_id' => 100, 'employee_id' => [1]], $models);

    expect($result->first()->departments)->toBe([]);
});
