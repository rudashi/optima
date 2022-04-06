<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Spatie\DataTransferObject\DataTransferObject;

class Employee extends DataTransferObject
{

    public int $id;
    public string $code;
    public string $firstname;
    public string $lastname;
    public string $email;
    public ?string $job_title = null;
    public int $department_id;
    public string $department_name;
    public string $company;
    public bool $deleted;

}
