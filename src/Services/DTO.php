<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;
use ReflectionClass;
use ReflectionProperty;
use Rudashi\Optima\Exceptions\IncorrectValueException;
use UnitEnum;

abstract class DTO implements Arrayable
{
    use Conditionable;

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

        if ($class->hasMethod('preValidation')) {
            static::preValidation($args);
        }

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
        $value = $args[$property->getName()];
        $cast = $this->casters[$property->getName()] ?? null;

        if ($cast instanceof Closure) {
            return $cast($value, $args);
        }

        if (is_string($cast)) {
            if (enum_exists($cast)) {
                /** @var UnitEnum $cast */
                return $cast::from($value);
            }

            if (function_exists($cast)) {
                return $cast($value);
            }

            return new $cast($value);
        }

        return match ($property->getType()?->getName()) {
            'DateTime',
            Carbon::class,
            \Illuminate\Support\Carbon::class => $value ? new Carbon($value) : $property->getDefaultValue(),
            default => $value
        };
    }

    protected function throwIncorrectValue(string $message): void
    {
        throw new IncorrectValueException($message);
    }
}
