<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

abstract class DTO implements Arrayable
{
    protected string $primaryKey = 'id';
    protected array $onlyKeys = [];
    protected array $appends = [];
    protected array $casters = [];

    public function __construct(...$args)
    {
        if (is_object($args[0] ?? null)) {
            $args = (array) $args[0];
        }
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new ReflectionClass($this);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if (! array_key_exists($property->getName(), $args)) {
                if ($property->hasDefaultValue() || $property->getType()?->allowsNull()) {
                    $property->setValue($this, $property->getDefaultValue());
                }
                continue;
            }

            $property->setValue($this, $this->castTo($property, $args));
        }

        foreach ($this->appends as $property => $callback) {
            if ($class->hasProperty($property)) {
                $class->getProperty($property)->setValue($this, $callback());
            }
        }
    }

    public function getKey(): mixed
    {
        return $this->{$this->getKeyName()};
    }

    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

    public function getAttribute(string $key): mixed
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }

        return null;
    }

    public function getAttributes(): array
    {
        return $this->toArray();
    }

    public function only(string ...$keys): static
    {
        $this->onlyKeys = [...$this->onlyKeys, ...$keys];

        return $this;
    }

    public function append(string $property, Closure $callback): static
    {
        $this->appends[$property] = $callback;

        return $this;
    }

    public function cast(string $property, mixed $caster): static
    {
        $this->casters[$property] = $caster;

        return $this;
    }

    public function all(): array
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys) > 0) {
            return array_intersect_key($this->all(), array_flip($this->onlyKeys));
        }

        return $this->all();
    }

    public static function get(string $key, array|object $attributes): mixed
    {
        if (self::filled($key, $attributes) === false) {
            return null;
        }

        return is_object($attributes) ? $attributes->{$key} : $attributes[$key];
    }

    public static function filled(string $key, array|object $attributes): bool
    {
        if (is_object($attributes)) {
            return property_exists($attributes, $key);
        }

        return array_key_exists($key, $attributes);
    }

    private function castTo(ReflectionProperty $property, array $args): mixed
    {
        $type = $property->getType()?->getName();
        $value = $args[$property->getName()];
        $cast = $this->casters[$property->getName()] ?? null;

        return match (true) {
            enum_exists(is_string($cast) ? $cast : '') => $cast::from($value),
            $cast instanceof Closure => $cast($value, $args),
            is_string($cast) => new $cast($value),
            default => match ($type) {
                'DateTime', 'Carbon' => new Carbon($value),
                default => $value
            },
        };
    }
}
