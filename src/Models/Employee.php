<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Rudashi\Optima\Services\Entity\Parser;

class Employee
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $email,
        public readonly ?string $job_title,
        public readonly int $department_id,
        public readonly string $department_name,
        public readonly string $company,
        public readonly ?string $rcp,
        public readonly bool $deleted,
    ) {
    }

    public static function make(object $data): self
    {
        return new self(
            id: (int) $data->id,
            code: $data->code,
            firstname: $data->firstname,
            lastname: $data->lastname,
            email: $data->email,
            job_title: Parser::for($data, 'job_title')->string(),
            department_id: (int) $data->department_id,
            department_name: $data->department_name,
            company: $data->company,
            rcp: Parser::for($data, 'rcp')->string(),
            deleted: (bool) $data->deleted,
        );
    }
}
