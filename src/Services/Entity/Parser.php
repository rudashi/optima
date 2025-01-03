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
            value: self::has($item, $key) ? $item->{$key} : $default
        );
    }

    /**
     * @return self<TValue>
     */
    public static function of(mixed $value): self
    {
        return new self($value);
    }

    public static function has(object $item, string $key): bool
    {
        return property_exists($item, $key);
    }

    public static function isEqual(object $item, string $key, mixed $value): bool
    {
        if (self::has($item, $key)) {
            return $item->{$key} === $value;
        }

        return false;
    }

    public function bool(bool $default = false): bool
    {
        return Entry::bool($this->value) ?? $default;
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

    /**
     * @template TEnum of \BackedEnum
     *
     * @param  class-string<TEnum>  $enum
     * @return TEnum|null
     */
    public function enum(string $enum, mixed $default = null): mixed
    {
        if (is_string($this->value) || is_int($this->value)) {
            if (is_int($enum::cases()[0]->value)) {
                return $enum::tryFrom((int) $this->value) ?? $default;
            }

            return $enum::tryFrom($this->value) ?? $default;
        }

        return $default;
    }

    public function int(?int $default = null): int|null
    {
        return Entry::int($this->value) ?? $default;
    }

    public function float(?float $default = null): float|null
    {
        return Entry::float($this->value) ?? $default;
    }

    public function return(): mixed
    {
        return $this->value;
    }

    public function string(?string $default = null): string|null
    {
        return $this->value !== null ? (string) $this->value : $default;
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

    /**
     * @param  \Closure(TValue): mixed  $callback
     */
    public function whenNotNull(Closure $callback, mixed $default = null): mixed
    {
        if ($this->value !== null) {
            return $callback($this->value) ?? $default;
        }

        return $this->value;
    }
}
