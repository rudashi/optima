<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

interface Relation
{
    /**
     * @param  iterable<int>  $relationId
     */
    public function handle(iterable $relationId): object;
}
