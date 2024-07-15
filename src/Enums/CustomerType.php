<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

use Rudashi\Optima\Contracts\Describable;

enum CustomerType: int implements Describable
{
    case RECIPIENT = 0;
    case SUPPLIER = 1;
    case COMPETITION = 2;
    case PARTNER = 3;
    case POTENTIAL = 4;

    public function description(): string
    {
        return match ($this) {
            self::RECIPIENT => __('Recipient'),
            self::SUPPLIER => __('Supplier'),
            self::COMPETITION => __('Competition'),
            self::PARTNER => __('Partner'),
            self::POTENTIAL => __('Potential'),
        };
    }

    public function equals(self $type): bool
    {
        return $this === $type;
    }
}
