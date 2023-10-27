<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Closure;

interface ValueObject
{
    public function getKey(): mixed;

    public function getKeyName(): string;

    public function getAttribute(string $key): mixed;

    public function getAttributes(): array;

    public function only(string ...$keys): static;

    public function append(string $property, Closure $callback): static;

    public function cast(string $property, mixed $caster): static;

    public function all(): array;

    /***
     * @template TWhenReturnType
     *
     * @param  (\Closure($this): TWhenReturnType)|bool  $value
     * @param  (callable($this): TWhenReturnType)|null  $callback
     * @param  (callable($this): TWhenReturnType)|null  $default
     * @return $this|TWhenReturnType
     */
    public function when($value, callable $callback = null, callable $default = null);

    public static function get(string $key, array|object $attributes): mixed;

    public static function filled(string $key, array|object $attributes): bool;
}
