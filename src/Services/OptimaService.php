<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Arr;

class OptimaService
{
    public static string $connection = 'optima';
    protected Connection $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db->connection(static::$connection);
    }

    public function query(): QueryBuilder
    {
        return $this->newQuery();
    }

    public function newQuery(): QueryBuilder
    {
        return new QueryBuilder(
            $this->db, $this->db->getQueryGrammar(), $this->db->getPostProcessor()
        );
    }

    public function parseIds(...$ids): array
    {
        return Arr::flatten($ids);
    }

}
