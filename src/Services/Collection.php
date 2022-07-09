<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Support\Collection as CollectionBase;

class Collection extends CollectionBase
{
    public function modelKeys(): array
    {
        return array_map(static fn (DTO $model) => $model->getKey(), $this->items);
    }

    public function pluckAll(array $values): array
    {
        return $this->map(fn ($item) => array_map(static function ($value) use ($item) {
            return is_array($item) ? $item[$value] : $item->{$value};
        }, $values))->flatten()->unique()->filter()->all();
    }
}
