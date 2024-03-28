<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum PaymentForm: int
{
    case CASH_PLN = 1;
    case PREPAYMENT_PLN = 19;
    case PREPAYMENT = 46;
    case BANK_TRANSFER_SEK = 28;
    case BANK_TRANSFER = 36;
    case BANK_TRANSFER_PLN = 44;

    public function description(): string
    {
        return match ($this) {
            self::CASH_PLN => 'gotówka',
            self::PREPAYMENT_PLN => 'przedpłata',
            self::PREPAYMENT => 'prepayment',
            self::BANK_TRANSFER_PLN => 'przelew bankowy',
            self::BANK_TRANSFER => 'bank transfer',
            self::BANK_TRANSFER_SEK => 'överföring',
        };
    }

    public function currency(): string
    {
        return match ($this) {
            self::CASH_PLN,
            self::PREPAYMENT_PLN,
            self::BANK_TRANSFER_PLN => Country::POLAND->currency(),
            default => '',
        };
    }

    public static function for(Country $country): array
    {
        return match ($country) {
            Country::POLAND => [
                self::CASH_PLN,
                self::PREPAYMENT_PLN,
                self::BANK_TRANSFER_PLN,
            ],
            Country::SWEDEN => [
                self::PREPAYMENT,
                self::BANK_TRANSFER_SEK,
            ],
            default => [
                self::PREPAYMENT,
                self::BANK_TRANSFER,
            ],
        };
    }

    public static function toSelect(): array
    {
        return array_map(
            callback: static fn ($item) => [
                'name' => $item->description(),
                'currency' => $item->currency(),
                'value' => $item->value,
            ],
            array: self::cases()
        );
    }
}
