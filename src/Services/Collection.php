<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Support\Collection as CollectionBase;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
class Collection extends CollectionBase
{
    /**
     * @template TMapValue of TValue
     *
     * @param  callable(TValue): TMapValue  $callback
     *
     * @return self<TKey, TMapValue>
     */
    public function attach(callable $callback): self
    {
        $this->items = $this->map(static fn ($item) => $callback($item))->all();

        return $this;
    }

    /**
     * @return array<int, int|string>
     */
    public function modelKeys(string $primaryKey = 'id'): array
    {
        return array_map(static function ($model) use ($primaryKey) {
            if (method_exists($model, 'primaryKey')) {
                return $model->primaryKey();
            }

            return $model->$primaryKey;
        }, $this->items);
    }

    /**
     * @param  array<int, string>  $values
     *
     * @return array<TKey, mixed>
     */
    public function pluckAll(array $values): array
    {
        return $this->map(fn ($item) => array_map(static fn ($v) => is_array($item) ? $item[$v] : $item->{$v}, $values))
            ->flatten()
            ->unique()
            ->filter()
            ->all();
    }
}
