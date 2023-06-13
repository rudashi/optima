<?php

declare(strict_types=1);

namespace Rudashi\Optima\Enums;

enum Country: string
{
    case POLAND = 'PL';
    case BELGIUM = 'BE';
    case BULGARIA = 'BG';
    case SAINT_BARTHELEMY = 'BL';
    case SWITZERLAND = 'CH';
    case CZECH_REPUBLIC = 'CZ';
    case GERMANY = 'DE';
    case DENMARK = 'DK';
    case ESTONIA = 'EE';
    case SPAIN = 'ES';
    case FINLAND = 'FI';
    case FRANCE = 'FR';
    case UNITED_KINGDOM = 'GB';
    case IRELAND = 'IE';
    case LUXEMBOURG = 'LU';
    case LATVIA = 'LV';
    case NETHERLANDS = 'NL';
    case NORWAY = 'NO';
    case PORTUGAL = 'PT';
    case SWEDEN = 'SE';
    case UNITED_STATES = 'US';
    case NULL = '';

    public static function of(string|null $name = null): self
    {
        if (! $name) {
            return self::NULL;
        }

        foreach (self::cases() as $case) {
            if ($case->description() === $name) {
                return $case;
            }
        }

        return self::NULL;
    }

    public function description(): string
    {
        return match ($this) {
            self::POLAND => __('Poland'),
            self::BELGIUM => __('Belgium'),
            self::BULGARIA => __('Bulgaria'),
            self::SAINT_BARTHELEMY => __('Saint Barthélemy'),
            self::SWITZERLAND => __('Switzerland'),
            self::CZECH_REPUBLIC => __('Czech Republic'),
            self::GERMANY => __('Germany'),
            self::DENMARK => __('Denmark'),
            self::ESTONIA => __('Estonia'),
            self::SPAIN => __('Spain'),
            self::FINLAND => __('Finland'),
            self::FRANCE => __('France'),
            self::UNITED_KINGDOM => __('United Kingdom'),
            self::IRELAND => __('Ireland'),
            self::LUXEMBOURG => __('Luxembourg'),
            self::LATVIA => __('Latvia'),
            self::NETHERLANDS => __('Netherlands'),
            self::NORWAY => __('Norway'),
            self::PORTUGAL => __('Portugal'),
            self::SWEDEN => __('Sweden'),
            self::UNITED_STATES => __('United States'),
            self::NULL => '',
        };
    }

    public function currency(): string
    {
        return match ($this) {
            self::POLAND => 'PLN',
            self::BELGIUM,
            self::SAINT_BARTHELEMY,
            self::GERMANY,
            self::ESTONIA,
            self::FRANCE,
            self::FINLAND,
            self::SPAIN,
            self::IRELAND,
            self::LUXEMBOURG,
            self::NETHERLANDS,
            self::LATVIA,
            self::PORTUGAL => 'EUR',
            self::BULGARIA => 'BGN',
            self::SWITZERLAND => 'CHF',
            self::CZECH_REPUBLIC => 'CZK',
            self::DENMARK => 'DKK',
            self::UNITED_KINGDOM => 'GBP',
            self::NORWAY => 'NOK',
            self::SWEDEN => 'SEK',
            self::UNITED_STATES => 'USD',
            self::NULL => '',
        };
    }
}
