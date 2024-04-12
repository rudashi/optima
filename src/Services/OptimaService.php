<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Arr;

class OptimaService
{
    public static string $connection = 'optima';
    protected ?string $connectionName = null;
    protected ConnectionResolverInterface $resolver;

    public function __construct(ConnectionResolverInterface $resolver, string $connection = null)
    {
        $this->resolver = $resolver;
        $this->connectionName = $connection ?? static::$connection;
    }

    public function getConnectionName(): string
    {
        return $this->connectionName;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->resolver->connection($this->getConnectionName());
    }

    public function setConnectionName($name): static
    {
        $this->connectionName = $name;

        return $this;
    }

    public function newQuery(): QueryBuilder
    {
        $connection = $this->getConnection();

        /** @phpstan-ignore-next-line  */
        return new QueryBuilder($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());
    }

    public function parseIds(...$ids): array
    {
        if (isset($ids[0]) && (is_array($ids[0]) || $ids[0] instanceof Collection)) {
            $ids = $ids[0];
        }

        return $ids instanceof Collection ? $ids->modelKeys() : Arr::flatten($ids);
    }
}
