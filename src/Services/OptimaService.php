<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PDOException;

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

    public static function connection(): ConnectionInterface
    {
        return DB::connection(self::$connection);
    }

    public function getConnectionName(): string
    {
        return $this->connectionName;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->resolver->connection($this->getConnectionName());
    }

    public function hasConnection(): bool
    {
        try {
            $this->getConnection()->getReadPDO();

            return true;
        } catch (PDOException) {
            return false;
        }
    }

    public function setConnectionName(string $name): static
    {
        $this->connectionName = $name;

        return $this;
    }

    /**
     * @return \Rudashi\Optima\Services\QueryBuilder<int, object>
     */
    public function newQuery(): QueryBuilder
    {
        $connection = $this->getConnection();

        /** @phpstan-ignore-next-line */
        return new QueryBuilder($connection, $connection->getQueryGrammar(), $connection->getPostProcessor());
    }

    /**
     * @param  mixed  ...$ids
     *
     * @return array<int, int|string>
     */
    public function parseIds(...$ids): array
    {
        if (isset($ids[0]) && (is_array($ids[0]) || $ids[0] instanceof Collection)) {
            $ids = $ids[0];
        }

        return $ids instanceof Collection ? $ids->modelKeys() : Arr::flatten($ids);
    }
}
