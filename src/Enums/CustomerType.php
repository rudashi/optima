<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum CustomerType: string
{
    case SUPPLIER = 'DOSTAWCA';
    case SUBCONTRACTOR = 'PODWYKONAWCA';
}
