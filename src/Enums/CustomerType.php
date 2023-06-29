<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum CustomerType: int
{
    case RECIPIENT = 0;
    case SUPPLIER = 1;
    case COMPETITION = 2;
    case PARTNER = 3;
    case POTENTIAL = 4;

    public function description(): string
    {
        return match($this) {
            self::RECIPIENT => __('recipient'),
            self::SUPPLIER => __('supplier'),
            self::COMPETITION => __('competition'),
            self::PARTNER => __('partner'),
            self::POTENTIAL => __('potential'),
        };
    }

    public function equals(self $type): bool
    {
        return $this === $type;
    }
}
