<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable
{
    /***
     * @return array<TKey, TValue>
     */
    public static function toArray(): array;
}
