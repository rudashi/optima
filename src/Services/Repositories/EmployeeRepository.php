<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services\Repositories;

use Illuminate\Database\Query\Expression;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Models\Employee;
use Rudashi\Optima\Services\OptimaService;

class EmployeeRepository
{

    public function __construct(
        private OptimaService $service
    ) {}

    public function find(string $code): Employee
    {
        return $this->findByCode($code);
    }

    public function findByCode(string $code): Employee
    {
        $data = $this->service->newQuery()
            ->from('CDN.Pracidx as employee')
            ->select([
                'employee.PRI_PraId as id',
                'employee.PRI_Kod as code',
                'employee.PRI_Imie1 as firstname',
                'employee.PRI_Nazwisko as lastname',
                'employee.PRI_Archiwalny as deleted',
                'employee.PRI_CntId as department_id',
                'department.CNT_Kod as department_name',
                'work.PRE_HDKEmail as email',
                'hr.DKM_Nazwa as job_title',
                new Expression("( SELECT CNT_Nazwa FROM CDN.Centra WHERE CNT_Nieaktywny = 0 and CNT_Nazwa != '' and CNT_ParentId is null) as company"),
            ])
            ->leftJoin('CDN.Centra as department', 'department.CNT_CntId', 'employee.PRI_CntId')
            ->leftJoin('CDN.PracEtaty as work', static function(JoinClause $join) {
                $join->on('work.PRE_PreId', new Expression('(SELECT MAX(PRE_PreId) FROM CDN.PracEtaty WHERE PRE_PraId = employee.PRI_PraId)'));
            })
            ->leftJoin('CDN.DaneKadMod as hr', 'hr.DKM_DkmId', 'work.PRE_ETADkmIdStanowisko')
            ->whereIn('employee.PRI_Typ', [1, 2])
            ->where('employee.PRI_Kod', $code)
            ->first();

        if ($data === null) {
            throw new RecordsNotFoundException(__('Given acronym :code is invalid or not in the OPTIMA.', ['code' => $code]));
        }

        if ((bool) $data->deleted === true) {
            throw new RecordsNotFoundException(__('Employee with given acronym :code is archived.', ['code' => $code]));
        }

        return new Employee((array) $data);
    }

}
