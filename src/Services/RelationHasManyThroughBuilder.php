<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

class RelationHasManyThroughBuilder extends RelationBuilder
{
    public function __construct(
        string $relationClass,
        private readonly string $through,
        string $ownerKey,
        private readonly string $localKey,
        private readonly string $firstKey,
        string $foreignKey,
        string $name
    ) {
        parent::__construct($name, $relationClass, $ownerKey, $foreignKey);
    }

    protected function handleRelation(Collection $models): array|object
    {
        $through = $models->map(fn ($item) => $item->{$this->through})->flatten();
        $constraints = $through->map(fn ($item) => $item->{$this->localKey});

        $items = $this->relation->handle($constraints->all());

        foreach ($items as $item) {
            $item->{$this->foreignKey} = array_unique($through
                ->where($this->localKey, '=', $item->{$this->ownerKey})
                ->pluck($this->foreignKey)
                ->all());
        }

        return $items;
    }

    protected function buildDictionary(object|array $models, string $key): array
    {
        $dictionary = [];

        foreach ($models as $relation) {
            $attributes = is_iterable($relation) ? $relation->get(0)?->{$key} : $relation->{$key};

            foreach ($attributes as $attribute) {
                $dictionary[$attribute][] = $relation;
            }
        }

        return $dictionary;
    }
}
