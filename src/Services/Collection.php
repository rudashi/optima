<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Support\Collection as CollectionBase;

class Collection extends CollectionBase
{
    public function attach(callable $callback): self
    {
        $this->items = $this->map(static fn ($item) => $callback($item))->all();

        return $this;
    }

    public function modelKeys(): array
    {
        return array_map(static fn (DTO $model) => $model->getKey(), $this->items);
    }

    public function pluckAll(array $values): array
    {
        return $this->map(fn ($item) => array_map(static fn ($v) => is_array($item) ? $item[$v] : $item->{$v}, $values))
            ->flatten()
            ->unique()
            ->filter()
            ->all();
    }
}
