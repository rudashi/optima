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

    /**
     * @template TWhenReturnType
     * @template TWhenParameter
     *
     * @param  (Closure($this): TWhenParameter)|bool  $value
     * @param  (callable($this, TWhenParameter): TWhenReturnType)|null  $callback
     * @param  (callable($this, TWhenParameter): TWhenReturnType)|null  $default
     * @return static
     */
    public function when(Closure|bool $value, callable $callback = null, callable $default = null): static;

    public static function get(string $key, array|object $attributes): mixed;

    public static function filled(string $key, array|object $attributes): bool;
}
