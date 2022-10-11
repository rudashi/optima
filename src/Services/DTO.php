<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\DataTransferObject;

class DTO extends DataTransferObject implements Arrayable
{
    protected string $primaryKey = 'id';

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

    public function has(string $key, array $attributes = null): bool
    {
        return isset($attributes[$key]);
    }

    public function filled(string $key, array $attributes = null): bool
    {
        return array_key_exists($key, $attributes ?? $this->all());
    }

    public function only(string ...$keys): static
    {
        $this->onlyKeys = [...$this->onlyKeys, ...$keys];

        return $this;
    }
}
