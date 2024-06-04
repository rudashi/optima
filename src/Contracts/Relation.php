<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

use Rudashi\Optima\Services\Collection;

interface Relation
{
    public function fetch(iterable $relationId, string $foreignKey): Collection;
}
