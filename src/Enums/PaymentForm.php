<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

use Rudashi\Optima\Contracts\Arrayable;
use Rudashi\Optima\Contracts\Describable;

enum PaymentForm: int implements Arrayable, Describable
{
    case CASH_PLN = 1;
    case PREPAYMENT_PLN = 19;
    case PREPAYMENT_PLN_100 = 62;
    case PREPAYMENT_PLN_50 = 63;
    case PREPAYMENT = 46;
    case PREPAYMENT_DKK_100 = 66;
    case PREPAYMENT_DKK_50 = 70;
    case PREPAYMENT_EUR_100_E = 64;
    case PREPAYMENT_EUR_50_E = 65;
    case PREPAYMENT_GBP_100 = 69;
    case PREPAYMENT_GBP_50 = 71;
    case PREPAYMENT_NOK_100 = 68;
    case PREPAYMENT_NOK_50 = 72;
    case PREPAYMENT_SEK_100 = 67;
    case PREPAYMENT_SEK_50 = 73;
    case BANK_TRANSFER = 36;
    case BANK_TRANSFER_PLN = 44;
    case BANK_TRANSFER_PLN_ING = 60;
    case BANK_TRANSFER_PLN_ERSTE = 61;
    case BANK_TRANSFER_DKK = 56;
    case BANK_TRANSFER_EUR_E = 55;
    case BANK_TRANSFER_EUR_I = 53;
    case BANK_TRANSFER_GBP = 57;
    case BANK_TRANSFER_NOK = 59;
    case BANK_TRANSFER_SEK = 58;

    public function description(): string
    {
        return match ($this) {
            self::CASH_PLN => 'gotówka',
            self::PREPAYMENT_PLN => 'przedpłata',
            self::PREPAYMENT_PLN_100 => 'przedpłata PLN 100%',
            self::PREPAYMENT_PLN_50 => 'przedpłata PLN 50%',
            self::PREPAYMENT => 'prepayment',
            self::PREPAYMENT_DKK_100 => 'prepayment DKK 100%',
            self::PREPAYMENT_DKK_50 => 'prepayment DKK 50%',
            self::PREPAYMENT_EUR_100_E => 'prepayment EUR 100%E',
            self::PREPAYMENT_EUR_50_E => 'prepayment EUR 50%-E',
            self::PREPAYMENT_GBP_100 => 'prepayment GBP 100%',
            self::PREPAYMENT_GBP_50 => 'prepayment GBP 50%',
            self::PREPAYMENT_NOK_100 => 'prepayment NOK 100%',
            self::PREPAYMENT_NOK_50 => 'prepayment NOK 50%',
            self::PREPAYMENT_SEK_100 => 'prepayment SEK 100%',
            self::PREPAYMENT_SEK_50 => 'prepayment SEK 50%',
            self::BANK_TRANSFER => 'bank transfer',
            self::BANK_TRANSFER_PLN => 'przelew bankowy',
            self::BANK_TRANSFER_PLN_ING => 'przelew PLN - ING',
            self::BANK_TRANSFER_PLN_ERSTE => 'przelew PLN - ERSTE',
            self::BANK_TRANSFER_DKK => 'bank transfer DKK',
            self::BANK_TRANSFER_EUR_E => 'bank transfer EUR-E',
            self::BANK_TRANSFER_EUR_I => 'bank transfer EUR-I',
            self::BANK_TRANSFER_GBP => 'bank transfer GBP',
            self::BANK_TRANSFER_NOK => 'bank transfer NOK',
            self::BANK_TRANSFER_SEK => 'bank transfer SEK',
        };
    }

    public function currency(): string
    {
        return match ($this) {
            self::CASH_PLN,
            self::PREPAYMENT_PLN,
            self::PREPAYMENT_PLN_100,
            self::PREPAYMENT_PLN_50,
            self::BANK_TRANSFER_PLN_ING,
            self::BANK_TRANSFER_PLN_ERSTE,
            self::BANK_TRANSFER_PLN => Country::POLAND->currency(),
            self::PREPAYMENT_DKK_100,
            self::PREPAYMENT_DKK_50,
            self::BANK_TRANSFER_DKK => Country::DENMARK->currency(),
            self::PREPAYMENT_GBP_100,
            self::PREPAYMENT_GBP_50,
            self::BANK_TRANSFER_GBP => Country::UNITED_KINGDOM->currency(),
            self::PREPAYMENT_NOK_100,
            self::PREPAYMENT_NOK_50,
            self::BANK_TRANSFER_NOK => Country::NORWAY->currency(),
            self::PREPAYMENT_SEK_100,
            self::PREPAYMENT_SEK_50,
            self::BANK_TRANSFER_SEK => Country::SWEDEN->currency(),
            default => '',
        };
    }

    /**
     * @return array<int, \Rudashi\Optima\Enums\PaymentForm>
     */
    public static function for(Country $country): array
    {
        return match ($country) {
            Country::POLAND => [
                self::CASH_PLN,
                self::PREPAYMENT_PLN,
                self::PREPAYMENT_PLN_100,
                self::PREPAYMENT_PLN_50,
                self::BANK_TRANSFER_PLN,
                self::BANK_TRANSFER_PLN_ING,
                self::BANK_TRANSFER_PLN_ERSTE,
            ],
            Country::SWEDEN => [
                self::PREPAYMENT_SEK_100,
                self::PREPAYMENT_SEK_50,
                self::BANK_TRANSFER_SEK,
            ],
            Country::NORWAY => [
                self::PREPAYMENT_NOK_100,
                self::PREPAYMENT_NOK_50,
                self::BANK_TRANSFER_NOK,
            ],
            Country::UNITED_KINGDOM => [
                self::PREPAYMENT_GBP_100,
                self::PREPAYMENT_GBP_50,
                self::BANK_TRANSFER_GBP,
            ],
            Country::DENMARK => [
                self::PREPAYMENT_DKK_100,
                self::PREPAYMENT_DKK_50,
                self::BANK_TRANSFER_DKK,
            ],
            default => [
                self::PREPAYMENT,
                self::BANK_TRANSFER,
            ],
        };
    }

    public static function toArray(): array
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
