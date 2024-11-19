<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

use Rudashi\Optima\Contracts\Describable;

enum CustomerEntity: int implements Describable
{
    case COMPANY = 0;
    case PERSON = 1;

    public function description(): string
    {
        return match ($this) {
            self::COMPANY => __('Business entity'),
            self::PERSON => __('Natural person'),
        };
    }
}
