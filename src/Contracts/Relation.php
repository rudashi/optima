<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

interface Relation
{
    public function fetch(iterable $relationId, string $localKey): object;
}
