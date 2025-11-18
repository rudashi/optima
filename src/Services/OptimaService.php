<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PDOException;

class OptimaService
{
    public static string $connection = 'optima';
    protected ?string $connectionName = null;
    protected DatabaseManager $resolver;

    public function __construct(DatabaseManager $resolver, string $connection = null)
    {
        $this->resolver = $resolver;
        $this->connectionName = $connection ?? static::$connection;
    }

    public static function connection(): Connection
    {
        return DB::connection(self::$connection);
    }

    public function getConnectionName(): string
    {
        return $this->connectionName;
    }

    public function getConnection(): Connection
    {
        return $this->resolver->connection($this->getConnectionName());
    }

    public function hasConnection(): bool
    {
        try {
            $this->getConnection()->getReadPdo();

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
     * @return \Rudashi\Optima\Services\QueryBuilder<int, \stdClass>
     */
    public function newQuery(): QueryBuilder
    {
        $connection = $this->getConnection();

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
