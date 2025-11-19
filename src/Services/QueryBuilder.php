<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Closure;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Rudashi\Optima\Contracts\Relation;
use stdClass;

/**
 * @template TKey of array-key
 *
 * @template-covariant TValue of \stdClass
 */
class QueryBuilder extends Builder
{
    /**
     * @var array<array-key, \Rudashi\Optima\Services\RelationBuilder>
     */
    protected array $relations = [];

    /**
     * Execute the query as a "select" statement.
     *
     * @param array<array-key, string> $columns
     *
     * @return \Rudashi\Optima\Services\Collection<array-key, \stdClass>
     */
    public function get($columns = ['*']): Collection
    {
        $models = new Collection($this->onceWithColumns(Arr::wrap($columns), function () {
            return $this->processor->processSelect($this, $this->runSelect());
        }));

        $this->loadRelations($models);

        return $this->applyAfterQueryCallbacks(
            isset($this->groupLimit) ? $this->withoutGroupLimitKeys($models) : $models
        );
    }

    /**
     * @param string[]|string $columns
     */
    public function first($columns = ['*']): stdClass|null
    {
        /** @var \stdClass|null */
        return parent::first($columns);
    }

    /**
     * Get items and run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param callable(stdClass, array-key): TMapValue $callback
     *
     * @return \Rudashi\Optima\Services\Collection<array-key, TMapValue>
     */
    public function getTo(callable $callback): Collection
    {
        return $this->get()->map($callback);
    }

    /**
     * @param class-string<\Rudashi\Optima\Contracts\Relation> $related
     *
     * @return self<array-key, \stdClass>
     */
    public function hasOne(string $related, string $ownerKey, string $foreignKey, string $relation): self
    {
        $this->relations[] = new RelationHasOneBuilder($relation, $related, $ownerKey, $foreignKey);

        return $this;
    }

    /**
     * @param \Closure|class-string<\Rudashi\Optima\Contracts\Relation> $related
     *
     * @return self<array-key, \stdClass>
     */
    public function hasMany(Closure|string $related, string $ownerKey, string $foreignKey, string $relation): self
    {
        $this->relations[] = new RelationBuilder(
            name: $relation,
            relationClass: $related instanceof Closure ? $this->makeRelation($related) : $related,
            ownerKey: $ownerKey,
            foreignKey: $foreignKey
        );

        return $this;
    }

    /**
     * @param class-string<\Rudashi\Optima\Contracts\Relation> $related
     *
     * @return self<array-key, \stdClass>
     */
    public function hasManyThrough(
        string $related,
        string $through,
        string $ownerKey,
        string $localKey,
        string $firstKey,
        string $foreignKey,
        string $relation
    ): self {
        $this->relations[] = new RelationHasManyThroughBuilder(...func_get_args());

        return $this;
    }

    /**
     * @return self<array-key, \stdClass>
     */
    public function noLock(): self
    {
        return $this->lock('WITH (NOLOCK)');
    }

    /**
     * @param \Rudashi\Optima\Services\Collection<array-key, \stdClass> $models
     *
     * @return \Rudashi\Optima\Services\Collection<array-key, \stdClass>
     */
    protected function loadRelations(Collection $models): Collection
    {
        if ($models->count() > 0) {
            foreach ($this->relations as $relation) {
                $models = $relation->match($relation->init($models), $models);
            }
        }

        return $models;
    }

    /**
     * @return \Rudashi\Optima\Contracts\Relation
     */
    private function makeRelation(Closure $related): object
    {
        return new readonly class ($this, $related) implements Relation {
            /**
             * @param \Rudashi\Optima\Services\QueryBuilder<int, \stdClass> $queryBuilder
             */
            public function __construct(
                private QueryBuilder $queryBuilder,
                private Closure $callable,
            ) {
            }

            /**
             * @return \Rudashi\Optima\Services\Collection<int, \stdClass>
             */
            public function handle(iterable $relationId): Collection
            {
                return ($this->callable)($this->queryBuilder->newQuery(), (array) $relationId)->get();
            }
        };
    }
}
