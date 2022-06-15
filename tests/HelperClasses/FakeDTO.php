<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HelperClasses;

use Rudashi\Optima\Services\DTO;

class FakeDTO extends DTO
{

    public int $id;
    public int $order_id;
    public string $name;
    public string $description;

}
