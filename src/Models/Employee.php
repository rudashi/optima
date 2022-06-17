<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Rudashi\Optima\Services\DTO;

class Employee extends DTO
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
    public ?string $rcp = null;
    public bool $deleted;

}
