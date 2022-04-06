<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Carbon\Carbon;
use Rudashi\Optima\Casters\CarbonCaster;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;

class Department extends DataTransferObject
{

    public int $id;
    public string $name;
    public ?int $parent_id = null;
    public string $user_code;
    #[CastWith(CarbonCaster::class)]
    public ?Carbon $user_date;

}
