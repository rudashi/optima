<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HelperClasses;

class CustomPrimaryDTO extends FakeDTO
{
    protected string $primaryKey = 'order_id';
}
