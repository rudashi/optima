<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

class RelationHasOneBuilder extends RelationBuilder
{
    protected function buildDictionary(object|array $models, string $key): array
    {
        if (is_array($models)) {
            $dictionary = [];

            foreach ($models as $relation) {
                $attribute = is_iterable($relation) ? $relation->get(0)?->{$key} : $relation->{$key};

                $dictionary[$attribute] = $relation;
            }

            return $dictionary;
        }

        return parent::buildDictionary($models, $key);
    }

    protected function defaultRelation(): null
    {
        return null;
    }
}
