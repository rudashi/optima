<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\DataTransferObject;

class Department extends DataTransferObject implements Arrayable
{

    public int $id;
    public string $name;
    public ?int $parent_id = null;
    public string $user_code;

}
