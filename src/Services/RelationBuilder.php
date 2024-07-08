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
        $this->relation = $this->newRelationInstance();
    }

    public function getKeyName(): string
    {
        return $this->ownerKey;
    }

    public function init(Collection $models): array|object
    {
        $items = $this->relation->handle($models->modelKeys($this->ownerKey));

        return $items instanceof Collection ? $items->all() : $items;
    }

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

    public function newRelationInstance(): Relation
    {
        return resolve($this->relationClass);
    }

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
}
