<?php

namespace App\Support;

class Number
{
    /**
     * تبدیل اعداد لاتین به فارسی
     */
    public static function fa($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];

        return str_replace($english, $persian, (string) $value);
    }

    /**
     * تبدیل اعداد فارسی به لاتین
     */
    public static function en($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $value = str_replace($persian, $english, (string) $value);
        $value = str_replace($arabic, $english, $value);

        return $value;
    }

    /**
     * فرمت پول با جداکننده هزارگان و اعداد فارسی
     */
    public static function money($value, bool $persian = true): string
    {
        if ($value === null || $value === '') {
            return $persian ? '۰' : '0';
        }
        $formatted = number_format((int) $value);
        return $persian ? self::fa($formatted) : $formatted;
    }
}
