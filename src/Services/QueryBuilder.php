<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class QueryBuilder extends Builder
{
    public function get($columns = ['*']): Collection
    {
        return new Collection($this->onceWithColumns(Arr::wrap($columns), function () {
            return $this->processor->processSelect($this, $this->runSelect());
        }));
    }
}
