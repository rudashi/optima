<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services\Entity;

use Carbon\CarbonInterface;
use Closure;
use Rudashi\Optima\Exceptions\IncorrectValueException;

/**
 * @template TValue of mixed
 */
class Parser
{
    /**
     * @param  TValue  $value
     */
    public function __construct(
        private readonly mixed $value
    ) {
    }

    /**
     * @return self<TValue>
     */
    public static function for(object $item, string $key, mixed $default = null): self
    {
        return new self(
            value: property_exists($item, $key) ? $item->{$key} : $default
        );
    }

    /**
     * @param  \Closure(TValue): mixed  $callback
     */
    public function call(Closure $callback, mixed $default = null): mixed
    {
        return $callback($this->value) ?? $default;
    }

    public function date(?CarbonInterface $default = null): CarbonInterface|null
    {
        return Entry::date($this->value) ?? $default;
    }

    public function int(?int $default = null): int|null
    {
        return Entry::int($this->value) ?? $default;
    }

    public function float(?float $default = null): float|null
    {
        return Entry::float($this->value) ?? $default;
    }

    public function string(?string $default = null): string|null
    {
        return $this->value ?? $default;
    }

    public function trim(?string $default = null): string|null
    {
        return Entry::trim($this->value) ?? $default;
    }

    /**
     * @param  \Closure(TValue): bool  $callback
     */
    public function throwWhen(Closure $callback, string $message = 'Incorrect value'): void
    {
        if ($callback($this->value)) {
            throw new IncorrectValueException($message);
        }
    }
}
