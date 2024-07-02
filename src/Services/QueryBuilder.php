<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Rudashi\Optima\Contracts\Relation;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 */
class QueryBuilder extends Builder
{
    protected array $relations = [];

    public function get($columns = ['*']): Collection
    {
        $models = new Collection($this->onceWithColumns(Arr::wrap($columns), function () {
            return $this->processor->processSelect($this, $this->runSelect());
        }));

        if ($models->count() > 0) {
            $this->loadRelations($models);
        }

        return $models;
    }

    /**
     * Get items and run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return Collection<TKey, TMapValue>
     */
    public function getTo(callable $callback): Collection
    {
        return $this->get()->map($callback);
    }

    public function with(string $related, string $ownerKey, string $foreignKey, string $relation): QueryBuilder
    {
        $this->relations[$relation] = [$ownerKey, $foreignKey, $related];

        return $this;
    }

    protected function loadRelations(Collection $models): Collection
    {
        foreach ($this->relations as $name => $relation) {
            [$ownerKey, $foreignKey, $relation] = $relation;

            $related = $this->getRelation($models->modelKeys($ownerKey), app($relation), $foreignKey);

            $models->attach(
                fn (object $item) => $this->setRelation($item, $name, $related[$item->{$ownerKey}] ?? null)
            );
        }

        return $models;
    }

    protected function getRelation(array $ownerKeys, Relation $relation, string $foreignKey): array
    {
        return $relation->fetch($ownerKeys, $foreignKey)->all();
    }

    protected function setRelation(object $item, string $name, Collection|null $relation = null): object
    {
        $item->{$name} = $relation ?? new Collection();

        return $item;
    }
}
