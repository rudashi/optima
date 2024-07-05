<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Rudashi\Optima\Contracts\Relation;

class RelationBuilder
{
    private readonly Relation $relation;

    public function __construct(
        private readonly string $name,
        private readonly string $relationClass,
        private readonly string $ownerKey,
        private readonly string $foreignKey,
    ) {
        $this->relation = app($this->relationClass);
    }

    public function getKeyName(): string
    {
        return $this->ownerKey;
    }

    public function init(Collection $models): array|object
    {
        $items = $this->relation->fetch($models->modelKeys($this->ownerKey), $this->foreignKey);

        return $items instanceof Collection ? $items->all() : $items;
    }

    public function match(object $model, object|array $related): object
    {
        $model->{$this->name} = is_array($related)
            ? $this->getRelationItems($related, $model->{$this->ownerKey})
            : $related;

        return $model;
    }

    public function getRelationItems(array $related, string $ownerKey): Collection
    {
        return $related[$ownerKey] ?? new Collection();
    }
}
