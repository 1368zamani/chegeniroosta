<?php

namespace App\Support;

class Number
{
    private const FA = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];

    public static function fa($value): string
    {
        if ($value === null || $value === '') return '';
        return str_replace(range(0, 9), self::FA, (string) $value);
    }

    public static function rialToToman(int $rial): float
    {
        return $rial / 10;
    }

    public static function money(int $rial, string $unit = 'rial'): string
    {
        $val = $unit === 'toman' ? self::rialToToman($rial) : $rial;
        return self::fa(number_format($val)) . ' ' . ($unit === 'toman' ? 'تومان' : 'ریال');
    }
}
