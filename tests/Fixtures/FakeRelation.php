<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Fixtures;

use Rudashi\Optima\Contracts\Relation;
use Rudashi\Optima\Services\Collection;

class FakeRelation implements Relation
{
    public array $receivedKeys = [];
    public int $calls = 0;

    public function __construct(
        private readonly array $rows = [],
    ) {
    }

    public function handle(iterable $relationId): Collection
    {
        ++$this->calls;
        $this->receivedKeys = (array) $relationId;

        return new Collection($this->rows);
    }
}
