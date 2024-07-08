<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue
 */
class QueryBuilder extends Builder
{
    /**
     * @var array<array-key, RelationBuilder>
     */
    protected array $relations = [];

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array  $columns
     * @return Collection
     */
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
        $this->relations[] = new RelationBuilder(
            name: $relation,
            relationClass: $related,
            ownerKey: $ownerKey,
            foreignKey: $foreignKey
        );

        return $this;
    }

    protected function loadRelations(Collection $models): Collection
    {
        foreach ($this->relations as $relation) {
            $models = $relation->match($relation->init($models), $models);
        }

        return $models;
    }
}
