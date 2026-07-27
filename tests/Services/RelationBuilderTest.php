<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Services\RelationBuilderTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\RelationBuilder;
use Rudashi\Optima\Tests\Fixtures\FakeRelation;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(RelationBuilder::class);

it('returns the owner key name', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');

    expect($builder->getKeyName())->toBe('id');
});

it('initializes every model with an empty array default', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1], (object) ['id' => 2]]);

    $builder->init($models);

    expect($models->first()->items)->toBe([])
        ->and($models->last()->items)->toBe([]);
});

it('passes owner keys to the relation', function () {
    $relation = new FakeRelation();
    $builder = new RelationBuilder('items', $relation, 'id', 'order_id');

    $builder->init(new Collection([(object) ['id' => 5], (object) ['id' => 9]]));

    expect($relation->receivedKeys)->toBe([5, 9]);
});

it('returns related items from init', function () {
    $related = [(object) ['order_id' => 5]];
    $builder = new RelationBuilder('items', new FakeRelation($related), 'id', 'order_id');

    expect($builder->init(new Collection([(object) ['id' => 5]])))->toBe($related);
});

it('groups related models per owner on match', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1], (object) ['id' => 2]]);
    $related = [
        (object) ['order_id' => 1, 'name' => 'A'],
        (object) ['order_id' => 1, 'name' => 'B'],
        (object) ['order_id' => 2, 'name' => 'C'],
    ];

    $builder->init($models);
    $result = $builder->match($related, $models);

    expect($result->first()->items)->toHaveCount(2)
        ->and($result->first()->items[0]->name)->toBe('A')
        ->and($result->first()->items[1]->name)->toBe('B')
        ->and($result->last()->items)->toHaveCount(1)
        ->and($result->last()->items[0]->name)->toBe('C');
});

it('keeps the default for models without related rows', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1]]);

    $builder->init($models);
    $result = $builder->match([], $models);

    expect($result->first()->items)->toBe([]);
});

it('assigns a single related object directly on match', function () {
    $builder = new RelationBuilder('item', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1], (object) ['id' => 2]]);
    $related = (object) ['order_id' => 1, 'name' => 'A'];

    $builder->init($models);
    $result = $builder->match($related, $models);

    expect($result->first()->item)->toBe($related)
        ->and($result->last()->item)->toBe([]);
});

it('reads the key from the first row of a nested collection', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1]]);
    $group = new Collection([
        (object) ['order_id' => 1, 'name' => 'A'],
        (object) ['order_id' => 99, 'name' => 'B'],
    ]);

    $builder->init($models);
    $result = $builder->match([$group], $models);

    expect($result->first()->items)->toHaveCount(1)
        ->and($result->first()->items[0])->toBe($group);
});

it('skips an empty nested collection of related rows', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1]]);

    $builder->init($models);
    $result = $builder->match([new Collection()], $models);

    expect($result->first()->items)->toBe([]);
});

it('reads the key from a non-iterable object with a get method', function () {
    $builder = new RelationBuilder('items', new FakeRelation(), 'id', 'order_id');
    $models = new Collection([(object) ['id' => 1]]);
    $related = new class () {
        public int $order_id = 1;

        public function get(int $index): mixed
        {
            return null;
        }
    };

    $builder->init($models);
    $result = $builder->match([$related], $models);

    expect($result->first()->items)->toHaveCount(1)
        ->and($result->first()->items[0])->toBe($related);
});

it('resolves a relation class from the container', function () {
    $relation = new FakeRelation();
    $this->app->instance(FakeRelation::class, $relation);

    $builder = new RelationBuilder('items', FakeRelation::class, 'id', 'order_id');

    $builder->init(new Collection([(object) ['id' => 1]]));

    expect($relation->calls)->toBe(1)
        ->and($builder->newRelationInstance(FakeRelation::class))->toBe($relation);
});

it('returns a relation instance as is', function () {
    $relation = new FakeRelation();
    $builder = new RelationBuilder('items', $relation, 'id', 'order_id');

    expect($builder->newRelationInstance($relation))->toBe($relation);
});
