<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

interface Relation
{
    public function handle(iterable $relationId): object;
}
