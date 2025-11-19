<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Rudashi\Optima\Contracts\Relation;

class RelationBuilder
{
    protected readonly Relation $relation;

    /**
     * @param class-string<\Rudashi\Optima\Contracts\Relation>|\Rudashi\Optima\Contracts\Relation $relationClass
     */
    public function __construct(
        protected readonly string $name,
        string|Relation $relationClass,
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
     * @param \Rudashi\Optima\Services\Collection<array-key, \stdClass> $models
     *
     * @return array<array-key, \stdClass>
     */
    public function init(Collection $models): array
    {
        $items = $this->handleRelation($models);

        foreach ($models as $model) {
            $model->{$this->name} = $this->defaultRelation();
        }

        return $items->all();
    }

    /**
     * @param array<array-key, \stdClass>|\stdClass $relatedModels
     * @param \Rudashi\Optima\Services\Collection<array-key, \stdClass> $models
     *
     * @return \Rudashi\Optima\Services\Collection<array-key, \stdClass>
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

    public function newRelationInstance(string|Relation $relationClass): Relation
    {
        if (is_string($relationClass)) {
            return resolve($relationClass);
        }

        return $relationClass;
    }

    /**
     * @param \Rudashi\Optima\Services\Collection<array-key, \stdClass> $models
     *
     * @return \Rudashi\Optima\Services\Collection<array-key, \stdClass>
     */
    protected function handleRelation(Collection $models): Collection
    {
        return $this->relation->handle($models->modelKeys($this->ownerKey));
    }

    /**
     * @param array<array-key, \stdClass>|\stdClass $models
     *
     * @return array<string, mixed>
     */
    protected function buildDictionary(object|array $models, string $key): array
    {
        $dictionary = [];

        if (is_array($models)) {
            foreach ($models as $relation) {
                $attribute = $this->getAttribute($relation, $key);

                $dictionary[$attribute][] = $relation;
            }
        }

        if (is_object($models)) {
            $attribute = $models->{$key};

            $dictionary[$attribute] = $models;
        }

        return $dictionary;
    }

    protected function getAttribute(object $relation, string $key): mixed
    {
        return is_iterable($relation) && method_exists($relation, 'get')
            ? $relation->get(0)?->{$key}
            : $relation->{$key};
    }

    protected function defaultRelation(): mixed
    {
        return [];
    }
}
