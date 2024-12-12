<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\HelperClasses;

class FakeDTO
{
    public function __construct(
        public int $id,
        public int|null $order_id = null,
        public string|null $name = null,
        public string|null $description = null,
    ) {
    }
}
