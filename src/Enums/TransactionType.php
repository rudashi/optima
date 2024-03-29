<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum TransactionType: int
{
    case NATIONAL = 0;
    case NON_EU = 1;
    case NON_EU_VAT_REFUND = 2;
    case INTRA_EU = 3;
    case INTRA_EU_TRIPARTITE = 4;
    case BUYER = 5;
    case EXTERNAL = 6;
    case EXTERNAL_NP = 7;
    case INTRA_EU_BUYER = 8;
    case NON_EU_BUYER = 9;
    case OSS = 10;

    public function description(): string
    {
        return match ($this) {
            self::NATIONAL => 'Krajowy',
            self::NON_EU => 'Pozaunijny',
            self::NON_EU_VAT_REFUND => 'Pozaunijny (zwrot VAT)',
            self::INTRA_EU => 'Wewnątrzunijny',
            self::INTRA_EU_TRIPARTITE => 'Wewnątrzunijny trójstronny',
            self::BUYER => 'Podatnikiem jest nabywca',
            self::EXTERNAL => 'Poza terytorium kraju',
            self::EXTERNAL_NP => 'Poza terytorium kraju (stawka NP)',
            self::INTRA_EU_BUYER => 'Wewnątrzunijny — podatnikiem jest nabywca',
            self::NON_EU_BUYER => 'Pozaunijny — podatnikiem jest nabywca',
            self::OSS => 'Procedura OSS',
        };
    }
}
