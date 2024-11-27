<?php

declare(strict_types=1);

namespace Rudashi\Optima\Contracts;

interface Arrayable
{
    /**
     * @return array<array-key, mixed>
     */
    public static function toArray(): array;
}
