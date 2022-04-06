<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services\Repositories;

use Illuminate\Database\RecordsNotFoundException;
use Rudashi\Optima\Enums\CustomerType;
use Rudashi\Optima\Models\Customer;
use Rudashi\Optima\Services\Collection;
use Rudashi\Optima\Services\OptimaService;
use Rudashi\Optima\Services\QueryBuilder;

class CustomerRepository
{

    public function __construct(
        private OptimaService $service
    ) {}

    public function findByCode(string $code, string $group = null): Customer
    {
        $data = $this->queryCustomer()
            ->where('Knt_Kod', $code)
            ->when($group, static function(QueryBuilder $query) use ($group) {
                return $query->where('Knt_Grupa', CustomerType::from($group)->value);
            })
            ->first();

        if ($data === null) {
            throw new RecordsNotFoundException(__('Given code :code is invalid or not in the OPTIMA.', ['code' => $code]));
        }

        return new Customer((array) $data);
    }

    public function find(...$ids): Collection
    {
        $data = $this->queryCustomer()
            ->whereIn('Knt_KntId', $this->service->parseIds($ids))
            ->get()
            ->keyBy('id');

        if ($data->isEmpty()) {
            throw new RecordsNotFoundException(__('Given id is invalid or not in the OPTIMA.'));
        }

        return $data->map(fn ($item) => new Customer((array) $item));
    }

    private function queryCustomer(): QueryBuilder
    {
        return $this->service->newQuery()
            ->from('CDN.Kontrahenci')
            ->select([
                'Knt_KntId as id',
                'Knt_Kod as code',
                'Knt_Nazwa1 as company',
                'Knt_Nazwa2 as name_line_two',
                'Knt_Nazwa3 as name_line_three',
                'Knt_Kraj as country',
                'Knt_Miasto as city',
                'Knt_KodPocztowy as postal_code',
                'Knt_Ulica as street',
                'Knt_NrDomu as building_number',
                'Knt_NrLokalu as suite_number',
                'Knt_Nip as nip',
                'Knt_Nieaktywny as deleted',
            ]);
    }

}
