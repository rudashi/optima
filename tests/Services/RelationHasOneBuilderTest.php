<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Services\RelationHasOneBuilderTest;

use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\RelationHasOneBuilder;
use Rudashi\Optima\Tests\Fixtures\FakeRelation;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(RelationHasOneBuilder::class);

it('initializes every model with a null default', function () {
    $builder = new RelationHasOneBuilder('address', new FakeRelation(), 'id', 'customer_id');
    $models = new Collection([(object) ['id' => 1], (object) ['id' => 2]]);

    $builder->init($models);

    expect($models->first()->address)->toBeNull()
        ->and($models->last()->address)->toBeNull();
});

it('assigns the single matching model on match', function () {
    $builder = new RelationHasOneBuilder('address', new FakeRelation(), 'id', 'customer_id');
    $models = new Collection([(object) ['id' => 1], (object) ['id' => 2]]);
    $related = [
        (object) ['customer_id' => 1, 'city' => 'GDAŃSK'],
    ];

    $builder->init($models);
    $result = $builder->match($related, $models);

    expect($result->first()->address)->toBe($related[0])
        ->and($result->last()->address)->toBeNull();
});

it('keeps the last related row when owners are duplicated', function () {
    $builder = new RelationHasOneBuilder('address', new FakeRelation(), 'id', 'customer_id');
    $models = new Collection([(object) ['id' => 1]]);
    $first = (object) ['customer_id' => 1, 'city' => 'GDAŃSK'];
    $second = (object) ['customer_id' => 1, 'city' => 'SOPOT'];

    $builder->init($models);
    $result = $builder->match([$first, $second], $models);

    expect($result->first()->address)->toBe($second);
});

it('assigns a single related object directly on match', function () {
    $builder = new RelationHasOneBuilder('address', new FakeRelation(), 'id', 'customer_id');
    $models = new Collection([(object) ['id' => 1]]);
    $related = (object) ['customer_id' => 1, 'city' => 'GDAŃSK'];

    $builder->init($models);
    $result = $builder->match($related, $models);

    expect($result->first()->address)->toBe($related);
});
