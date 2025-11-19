<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Rudashi\Optima\Services\Entity\Parser;
use stdClass;

readonly class Employee
{
    public function __construct(
        public int $id,
        public string $code,
        public string $firstname,
        public string $lastname,
        public string $email,
        public ?string $job_title,
        public int $department_id,
        public string $department_name,
        public string $company,
        public ?string $rcp,
        public bool $deleted,
    ) {
    }

    public static function make(stdClass $data): self
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
