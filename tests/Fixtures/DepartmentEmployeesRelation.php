<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Fixtures;

use Rudashi\Optima\Contracts\Relation;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;

class DepartmentEmployeesRelation implements Relation
{
    public int $calls = 0;

    public function __construct(
        private readonly OptimaService $service,
    ) {
    }

    public function handle(iterable $relationId): Collection
    {
        ++$this->calls;

        return $this->service->newQuery()
            ->from('CDN.Pracidx')
            ->whereIn('PRI_CntId', (array) $relationId)
            ->orderBy('PRI_PraId')
            ->getTo(fn ($row) => (object) [
                'dept_id' => (int) $row->PRI_CntId,
                'code' => (string) $row->PRI_Kod,
            ]);
    }
}
