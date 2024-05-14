<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Support\Collection as CollectionBase;

/**
 * @method self map(callable $callback)
 * @method self mapWithKeys(callable $callback)
 */
class Collection extends CollectionBase
{
    public function attach(callable $callback): self
    {
        $this->items = $this->map(static fn ($item) => $callback($item))->all();

        return $this;
    }

    public function modelKeys(string $primaryKey = 'id'): array
    {
        return array_map(static function ($model) use ($primaryKey) {
            if ($model instanceof DTO) {
                return $model->getKey();
            }

            return $model->$primaryKey;
        }, $this->items);
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
