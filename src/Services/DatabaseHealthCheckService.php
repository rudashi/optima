<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Exception;
use Illuminate\Database\DatabaseManager;

class DatabaseHealthCheckService
{
    public const PROBLEM = 'problem';
    public const OK = 'ok';

    public function __construct(
        protected DatabaseManager $db
    ) {
    }

    public function status(): array
    {
        try {
            $this->db->connection(OptimaService::$connection)->getPdo();
        } catch (Exception $e) {
            return $this->problem('Could not connect to db', [
                'connection' => OptimaService::$connection,
                'exception' => $this->exceptionContext($e),
            ]);
        }

        return $this->okay();
    }

    public function problem(string $message = '', array $context = []): array
    {
        return [
            'status' => self::PROBLEM,
            'message' => $message,
            'context' => $context,
        ];
    }

    public function okay(array $context = []): array
    {
        return [
            'status' => self::OK,
            'context' => $context,
        ];
    }

    private function exceptionContext(Exception $e): array
    {
        return [
            'error' => $e->getMessage(),
            'class' => get_class($e),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ];
    }
}
