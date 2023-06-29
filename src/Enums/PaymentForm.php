<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum PaymentForm: int
{
    case CASH_PL = 1;
    case PREPAYMENT_PL = 19;
    case PREPAYMENT = 46;
    case BANK_TRANSFER_SKK = 28;
    case BANK_TRANSFER = 36;
    case BANK_TRANSFER_PL = 44;

    public function description(): string
    {
        return match ($this) {
            self::CASH_PL => 'gotówka',
            self::PREPAYMENT_PL => 'przedpłata',
            self::PREPAYMENT => 'prepayment',
            self::BANK_TRANSFER_PL => 'przelew bankowy',
            self::BANK_TRANSFER => 'bank transfer',
            self::BANK_TRANSFER_SKK => 'överföring',
        };
    }

    public static function for(Country $country): array
    {
        return match($country) {
            Country::POLAND => [
                self::CASH_PL,
                self::PREPAYMENT_PL,
                self::BANK_TRANSFER_PL,
            ],
            Country::SWEDEN => [
                self::PREPAYMENT,
                self::BANK_TRANSFER_SKK,
            ],
            default => [
                self::PREPAYMENT,
                self::BANK_TRANSFER,
            ],
        };
    }
}
