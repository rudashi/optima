<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Fixtures;

use Rudashi\Optima\Contracts\Relation;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;

class DepartmentManagerRelation implements Relation
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
            ->from('CDN.CentraKierownicy')
            ->whereIn('CNK_CntId', (array) $relationId)
            ->getTo(fn ($row) => (object) [
                'dept_id' => (int) $row->CNK_CntId,
                'manager_id' => (int) $row->CNK_PraId,
            ]);
    }
}
