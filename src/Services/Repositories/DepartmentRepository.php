<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services\Repositories;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Department;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;

class DepartmentRepository
{
    public function __construct(
        private readonly OptimaService $service
    ) {
    }

    /**
     * @return \Rudashi\Optima\Services\Collection<int, \Rudashi\Optima\Models\Department>
     */
    public function all(): Collection
    {
        return $this->queryDepartment()
            ->orderBy('CNT_CntId')
            ->getTo(fn ($item) => Department::make($item));
    }

    public function find(string $code): Department
    {
        return $this->findByCode($code);
    }

    public function findByCode(string $code): Department
    {
        $data = $this->queryDepartment()
            ->where('CNT_Kod', strtoupper($code))
            ->first();

        if ($data === null) {
            throw new RecordsNotFoundException(__('Given code :code is invalid or not in the OPTIMA.', ['code' => $code]));
        }

        return Department::make($data);
    }

    /**
     * @return \Rudashi\Optima\Services\QueryBuilder<int, object>
     */
    private function queryDepartment(): QueryBuilder
    {
        return $this->service->newQuery()
            ->from('CDN.Centra')
            ->select([
                'CNT_CntId as id',
                'CNT_Nazwa as name',
                'CNT_ParentId as parent_id',
                'PRI_Kod as user_code',
            ])
            ->leftJoin('CDN.CentraKierownicy', 'CNT_CntId', 'CNK_CntId')
            ->leftJoin('CDN.Pracidx', 'CNK_PraId', 'PRI_PraId')
            ->where('CNT_Nazwa', '!=', '')
            ->where('CNT_Nieaktywny', 0)
            ->whereIn('PRI_Typ', [1, 2])
            ->where('PRI_Archiwalny', 0)
            ->where('CNK_Rodzaj', 0);
    }
}
