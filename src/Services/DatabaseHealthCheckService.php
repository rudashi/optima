<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Exception;
use Illuminate\Database\DatabaseManager;

class DatabaseHealthCheckService
{
    public const string PROBLEM = 'problem';
    public const string OK = 'ok';

    public function __construct(
        protected DatabaseManager $db
    ) {
    }

    /**
     * @return array{status: string, message: string, context: array<array-key, mixed>}
     */
    public function status(): array
    {
        try {
            $this->db->connection(OptimaService::$connection)->getReadPdo();
        } catch (Exception $e) {
            return $this->problem('Could not connect to db', [
                'connection' => OptimaService::$connection,
                'exception' => $this->exceptionContext($e),
            ]);
        }

        return $this->okay();
    }

    /**
     * @param  array<array-key, mixed>  $context
     *
     * @return array{status: string, message: string, context: array<array-key, mixed>}
     */
    public function problem(string $message = '', array $context = []): array
    {
        return [
            'status' => self::PROBLEM,
            'message' => $message,
            'context' => $context,
        ];
    }

    /**
     * @param  array<array-key, mixed>  $context
     *
     * @return array{status: string, message: string, context: array<array-key, mixed>}
     */
    public function okay(array $context = []): array
    {
        return [
            'status' => self::OK,
            'message' => 'Database connection is okay',
            'context' => $context,
        ];
    }

    /**
     * @return array{error: string, class: string, line: int, file: string}
     */
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
