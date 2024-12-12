<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HelperClasses;

class CustomPrimaryDTO extends FakeDTO
{
    public function primaryKey(): int
    {
        return $this->order_id;
    }
}
