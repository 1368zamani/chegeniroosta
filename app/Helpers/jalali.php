<?php

if (!function_exists('gregorian_to_jalali_calc')) {
    /**
     * تبدیل تاریخ میلادی به شمسی
     * @return array [$jy, $jm, $jd]
     */
    function gregorian_to_jalali_calc($gy, $gm, $gd): array
    {
        $g_d_m = [0,31,59,90,120,151,181,212,243,273,304,334];
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = 355666 + (365 * $gy) + ((int)(($gy2 + 3) / 4))
              - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400))
              + $gd + $g_d_m[$gm - 1];
        $jy = -1595 + (33 * ((int)($days / 12053)));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        if ($days < 186) {
            $jm = 1 + (int)($days / 31);
            $jd = 1 + ($days % 31);
        } else {
            $jm = 7 + (int)(($days - 186) / 30);
            $jd = 1 + (($days - 186) % 30);
        }
        return [$jy, $jm, $jd];
    }
}

if (!function_exists('jalali')) {
    /**
     * نمایش تاریخ به صورت شمسی
     * 
     * @param mixed $date تاریخ (Carbon یا رشته)
     * @param string $format فرمت خروجی (Y/m/d H:i)
     * @param string $placeholder مقدار جایگزین اگر تاریخ خالی بود
     */
    function jalali($date, string $format = 'Y/m/d', string $placeholder = '—'): string
    {
        if (empty($date)) {
            return $placeholder;
        }

        try {
            if (is_string($date)) {
                $date = \Carbon\Carbon::parse($date);
            } elseif (!$date instanceof \Carbon\Carbon && !$date instanceof \DateTime) {
                return $placeholder;
            }
        } catch (\Throwable $e) {
            return $placeholder;
        }

        $gy = (int) $date->format('Y');
        $gm = (int) $date->format('n');
        $gd = (int) $date->format('j');

        [$jy, $jm, $jd] = gregorian_to_jalali_calc($gy, $gm, $gd);

        // جایگزینی کاراکترها
        $out = $format;
        $out = str_replace('Y', (string) $jy, $out);
        $out = str_replace('m', str_pad((string) $jm, 2, '0', STR_PAD_LEFT), $out);
        $out = str_replace('d', str_pad((string) $jd, 2, '0', STR_PAD_LEFT), $out);

        // ساعت دقیقه
        if (strpos($format, 'H:i') !== false) {
            $out = str_replace('H:i', $date->format('H:i'), $out);
        }

        return $out;
    }
}
