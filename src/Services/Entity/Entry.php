<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services\Entity;

use Carbon\Carbon;
use Carbon\CarbonInterface;

class Entry
{
    public static function date(mixed $value): ?CarbonInterface
    {
        if ($value === null) {
            return null;
        }

        return new Carbon($value);
    }

    /**
     * Parse the property to an int or returns null.
     */
    public static function int(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        return (int) $value;
    }

    /**
     * Parse the property to a float or returns null.
     */
    public static function float(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        return (float) $value;
    }

    /**
     * Parse the property to a trimmed string or returns null.
     */
    public static function trim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
    }
}
