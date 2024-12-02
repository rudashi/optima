<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Support\Enumerable;
use Rudashi\Optima\Contracts\Relation;

class RelationBuilder
{
    protected readonly Relation $relation;

    /**
     * @param  class-string<\Rudashi\Optima\Contracts\Relation>  $relationClass
     */
    public function __construct(
        protected readonly string $name,
        string $relationClass,
        protected readonly string $ownerKey,
        protected readonly string $foreignKey,
    ) {
        $this->relation = $this->newRelationInstance($relationClass);
    }

    public function getKeyName(): string
    {
        return $this->ownerKey;
    }

    /**
     * @template TKey of array-key
     * @template TValue of \stdClass
     *
     * @param  \Rudashi\Optima\Services\Collection<TKey, TValue>  $models
     *
     * @return array<TKey, TValue>|object
     */
    public function init(Collection $models): array|object
    {
        $items = $this->handleRelation($models);

        foreach ($models as $model) {
            $model->{$this->name} = $this->defaultRelation();
        }

        return $items instanceof Enumerable ? $items->all() : $items;
    }

    /**
     * @param  array<array-key, object>|object  $relatedModels
     * @param  \Rudashi\Optima\Services\Collection<array-key, object>  $models
     *
     * @return \Rudashi\Optima\Services\Collection<array-key, object>
     */
    public function match(object|array $relatedModels, Collection $models): Collection
    {
        $dictionary = $this->buildDictionary($relatedModels, $this->foreignKey);

        foreach ($models as $model) {
            $attribute = $model->{$this->ownerKey};

            if (isset($dictionary[$attribute])) {
                $model->{$this->name} = $dictionary[$attribute];
            }
        }

        return $models;
    }

    /**
     * @template TClass
     *
     * @param  class-string<TClass>  $relationClass
     *
     * @return TClass
     */
    public function newRelationInstance(string $relationClass)
    {
        return resolve($relationClass);
    }

    /**
     * @param  \Rudashi\Optima\Services\Collection<array-key, \stdClass>  $models
     *
     * @return array<array-key, object>|object
     */
    protected function handleRelation(Collection $models): array|object
    {
        return $this->relation->handle($models->modelKeys($this->ownerKey));
    }

    /**
     * @param  array<array-key, object>  $models
     *
     * @return array<string, mixed>
     */
    protected function buildDictionary(object|array $models, string $key): array
    {
        $dictionary = [];

        if (is_array($models)) {
            foreach ($models as $relation) {
                $attribute = is_iterable($relation) ? $relation->get(0)?->{$key} : $relation->{$key};

                $dictionary[$attribute][] = $relation;
            }
        }

        if (is_object($models)) {
            $attribute = $models->{$key};

            $dictionary[$attribute] = $models;
        }

        return $dictionary;
    }

    protected function defaultRelation(): mixed
    {
        return [];
    }
}
