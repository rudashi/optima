<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Fixtures;

class CustomPrimaryDTO extends FakeDTO
{
    public function primaryKey(): int
    {
        return $this->order_id;
    }
}
