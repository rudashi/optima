<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

use Rudashi\Optima\Services\Collection;

interface Relation
{
    /**
     * @param iterable<int|string> $relationId
     *
     * @return \Rudashi\Optima\Services\Collection<int, \stdClass|mixed>
     */
    public function handle(iterable $relationId): Collection;
}
