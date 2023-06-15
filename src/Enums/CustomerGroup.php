<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum CustomerGroup: string
{
    case TRADING_COMPANY = 'FIRMA HANDLOWA';
    case HEALTHCARE = 'OPIEKA ZDROWOTNA';
    case FOREIGN_COMPANY = 'ZAGRANICZNA FIRMA';
    case PUBLISHING_HOUSE = 'WYDAWNICTWO';
    case INDIVIDUAL = 'OSOBA FIZYCZNA';
    case INSTITUTION = 'INSTYTUCJA';
    case SERVICE_COMPANY = 'FIRMA USŁUGOWA';
    case UNIVERSITY = 'WYŻSZA UCZELNIA';
    case TRANSPORT = 'TRANSPORTOWA';
    case SUPPLIER = 'DOSTAWCA';
    case SUBCONTRACTOR = 'PODWYKONAWCA';
}
