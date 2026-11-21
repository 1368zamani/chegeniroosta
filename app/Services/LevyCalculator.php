<?php

namespace App\Services;

use App\Models\LevyVillageParam;

/**
 * محاسبهٔ عوارض احداث بنا — کپی دقیق فرمولهای فایل اکسل مرجع.
 *
 * سازگار با خروجی مدل LevyFile (همان فرمولها، همان گردکردن):
 *   N = M × 0.14        → ضریب نهایی
 *   S = Q × N × O       → عوارض مسکونی
 *   T = N × مساحت × P   → عوارض تراکم (مساحت طبق استثناء T64: Q یا R)
 *   U = MAX(S, T)       → مبلغ نهایی
 *
 * هیچ ضریب یا قانون دیگری اضافه نشده است.
 */
class LevyCalculator
{
    /**
     * @param LevyVillageParam|null $params ضرایب مخصوص روستا
     * @param float $constructionArea مساحت ساخت (Q)
     * @param float $propertyArea مساحت ملک (R)
     * @return array<string, int|float>
     */
    public static function construction(?LevyVillageParam $params, float $constructionArea, float $propertyArea = 0.0): array
    {
        if (!$params) {
            return [
                'transaction_value' => 0, 'final_coefficient' => 0,
                'construction_coefficient' => 0, 'density_coefficient' => 0,
                'construction_area' => $constructionArea, 'property_area' => $propertyArea,
                'density_area' => 0.0, 'density_area_source' => 'Q',
                'residential_levy' => 0, 'density_levy' => 0, 'final_levy' => 0,
            ];
        }

        $m = (int) $params->transaction_value;                 // M
        $n = (int) round($m * 0.14);                           // N
        $o = (int) $params->construction_coefficient;          // O
        $p = (int) $params->density_coefficient;               // P

        $q = $constructionArea;                                // Q
        $r = $propertyArea;                                    // R
        $densityArea = $params->density_area_source === 'R' ? $r : $q;

        $s = (int) round($q * $n * $o);                        // S
        $t = (int) round($n * $densityArea * $p);              // T
        $u = (int) max($s, $t);                                // U

        return [
            'transaction_value'        => $m,
            'final_coefficient'        => $n,
            'construction_coefficient' => $o,
            'density_coefficient'      => $p,
            'construction_area'        => $q,
            'property_area'            => $r,
            'density_area'             => $densityArea,
            'density_area_source'      => $params->density_area_source,
            'residential_levy'         => $s,
            'density_levy'             => $t,
            'final_levy'               => $u,
        ];
    }
}
