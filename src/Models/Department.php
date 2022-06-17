<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Rudashi\Optima\Services\DTO;

class Department extends DTO
{

    public int $id;
    public string $name;
    public ?int $parent_id = null;
    public string $user_code;

}
