<?php

namespace App\Services;

use App\Models\FeeTariff;
use InvalidArgumentException;

/**
 * محاسبه‌گر عوارض بر اساس تعرفه (fee_tariffs) و پارامترهای ورودی کاربر.
 *
 * خروجی شامل:
 *  - total  : مبلغ نهایی (ریال)
 *  - steps  : مراحل محاسبه برای نمایش شفاف به کاربر
 *  - snapshot : اسنپ‌شات تعرفه که در bills ذخیره می‌شود
 *
 * تمام فرمول‌ها از دیتابیس خوانده می‌شوند و هیچ نرخی در کد ثابت نیست.
 */
class FeeCalculator
{
    /**
     * @param FeeTariff $tariff تعرفه فعال (از DB)
     * @param array     $inputs ورودی‌های کاربر مثل ['area'=>120, 'floors'=>2, 'units'=>3]
     * @return array{total:int, steps:array, snapshot:array}
     */
    public function calculate(FeeTariff $tariff, array $inputs): array
    {
        $this->validateInputs($tariff, $inputs);

        $steps    = [];
        $subtotal = $this->dispatchFormula($tariff, $inputs, $steps);

        // اعمال ضریب عمومی (اگر فرمول خاص آن را اعمال نکرده باشد)
        $coef = (float) $tariff->coefficient;
        if ($coef != 1.0 && $tariff->formula_type !== 'area_x_rate_x_coef') {
            $before   = $subtotal;
            $subtotal = (int) round($subtotal * $coef);
            $steps[]  = [
                'label'  => 'اعمال ضریب',
                'detail' => '× ' . $coef,
                'amount' => $subtotal - $before,
            ];
        }

        return [
            'total'    => max(0, $subtotal),
            'steps'    => $steps,
            'snapshot' => [
                'fee_tariff_id'   => $tariff->id,
                'fee_type_id'     => $tariff->fee_type_id,
                'fee_type_name'   => $tariff->feeType?->name,
                'category_name'   => $tariff->feeType?->category?->name,
                'year'            => $tariff->year,
                'formula_type'    => $tariff->formula_type,
                'formula_display' => $this->formatFormula($tariff),
                'unit'            => $tariff->unit,
                'inputs'          => $inputs,
            ],
        ];
    }

    private function dispatchFormula(FeeTariff $t, array $in, array &$steps): int
    {
        return match ($t->formula_type) {
            'fixed'                  => $this->applyFixed($t, $steps),
            'area_x_rate'            => $this->applyAreaXRate($t, $in, $steps),
            'area_x_rate_x_coef'     => $this->applyAreaXRateXCoef($t, $in, $steps),
            'units_x_rate'           => $this->applyUnitsXRate($t, $in, $steps),
            'base_plus_units_x_rate' => $this->applyBasePlusUnits($t, $in, $steps),
            'custom'                 => $this->applyCustom($t, $in, $steps),
            default                  => throw new InvalidArgumentException(
                "نوع فرمول نامعتبر است: {$t->formula_type}"
            ),
        };
    }

    /**
     * اعتبارسنجی ورودی‌ها بر اساس fee_parameters هر نوع.
     * اگر پارامتر الزامی نیامده باشد یا عدد منفی باشد، استثنا می‌دهد.
     */
    private function validateInputs(FeeTariff $tariff, array $inputs): void
    {
        foreach ($tariff->feeType->parameters as $param) {
            if ($param->is_required && !array_key_exists($param->key, $inputs)) {
                throw new InvalidArgumentException("پارامتر «{$param->label}» الزامی است.");
            }
            if (!array_key_exists($param->key, $inputs)) {
                continue;
            }
            if ($param->input_type === 'number') {
                $v = $inputs[$param->key];
                if (!is_numeric($v)) {
                    throw new InvalidArgumentException("پارامتر «{$param->label}» باید عدد باشد.");
                }
                if ((float) $v < 0) {
                    throw new InvalidArgumentException("پارامتر «{$param->label}» نمی‌تواند منفی باشد.");
                }
            }
        }
    }

    private function applyFixed(FeeTariff $t, array &$steps): int
    {
        $steps[] = ['label' => 'مبلغ ثابت', 'detail' => '—', 'amount' => (int) $t->base_amount];
        return (int) $t->base_amount;
    }

    private function applyAreaXRate(FeeTariff $t, array $in, array &$steps): int
    {
        $area   = (float) ($in['area'] ?? 0);
        $amount = (int) round($area * $t->unit_rate);
        $steps[] = [
            'label'  => 'مساحت × نرخ هر متر',
            'detail' => rtrim(rtrim(number_format($area, 2, '.', ''), '0'), '.')
                        . ' م² × ' . number_format((int) $t->unit_rate) . ' ریال',
            'amount' => $amount,
        ];
        return $amount;
    }

    private function applyAreaXRateXCoef(FeeTariff $t, array $in, array &$steps): int
    {
        $area   = (float) ($in['area'] ?? 0);
        $coef   = (float) $t->coefficient;
        $before = (int) round($area * $t->unit_rate);
        $amount = (int) round($before * $coef);

        $steps[] = [
            'label'  => 'مساحت × نرخ',
            'detail' => rtrim(rtrim(number_format($area, 2, '.', ''), '0'), '.')
                        . ' م² × ' . number_format((int) $t->unit_rate) . ' ریال',
            'amount' => $before,
        ];
        $steps[] = [
            'label'  => 'اعمال ضریب',
            'detail' => '× ' . $coef,
            'amount' => $amount - $before,
        ];
        return $amount;
    }

    private function applyUnitsXRate(FeeTariff $t, array $in, array &$steps): int
    {
        $units  = (float) ($in['units'] ?? 1);
        $amount = (int) round($units * $t->unit_rate);
        $steps[] = [
            'label'  => 'تعداد واحد × نرخ',
            'detail' => $units . ' × ' . number_format((int) $t->unit_rate) . ' ریال',
            'amount' => $amount,
        ];
        return $amount;
    }

    private function applyBasePlusUnits(FeeTariff $t, array $in, array &$steps): int
    {
        $units  = (float) ($in['units'] ?? 1);
        $var    = (int) round($units * $t->unit_rate);
        $amount = (int) $t->base_amount + $var;

        $steps[] = ['label' => 'مبلغ پایه', 'detail' => '—', 'amount' => (int) $t->base_amount];
        $steps[] = [
            'label'  => 'تعداد واحد × نرخ',
            'detail' => $units . ' × ' . number_format((int) $t->unit_rate) . ' ریال',
            'amount' => $var,
        ];
        return $amount;
    }

    /**
     * فرمول سفارشی مثل: area * 50000 + floors * 100000
     * فقط متغیرهای عددی و عملگرهای ساده مجاز هستند.
     */
    private function applyCustom(FeeTariff $t, array $in, array &$steps): int
    {
        $expr = trim((string) ($t->formula_expression ?? '0'));

        // جایگزینی نام متغیرها با مقدار عددی داخل پرانتز
        $safe = preg_replace_callback('/[a-zA-Z_][a-zA-Z0-9_]*/', function ($m) use ($in) {
            $key = $m[0];
            if (!array_key_exists($key, $in)) {
                throw new InvalidArgumentException("متغیر «{$key}» در فرمول تعریف نشده.");
            }
            return '(' . (float) $in[$key] . ')';
        }, $expr);

        // فقط کاراکترهای مجاز باقی بمانند (جلوگیری از تزریق)
        if (!preg_match('/^[\s0-9\.\+\-\*\/\(\)]+$/', $safe)) {
            throw new InvalidArgumentException('فرمول سفارشی حاوی کاراکتر غیرمجاز است.');
        }

        $amount = (int) round(eval("return {$safe};"));
        $steps[] = ['label' => 'محاسبه فرمول سفارشی', 'detail' => $expr, 'amount' => $amount];
        return $amount;
    }

    private function formatFormula(FeeTariff $t): string
    {
        return match ($t->formula_type) {
            'fixed'                  => 'مبلغ ثابت',
            'area_x_rate'            => 'مساحت × نرخ هر متر',
            'area_x_rate_x_coef'     => 'مساحت × نرخ × ضریب',
            'units_x_rate'           => 'تعداد واحد × نرخ',
            'base_plus_units_x_rate' => 'مبلغ پایه + (تعداد واحد × نرخ)',
            'custom'                 => $t->formula_expression ?? 'فرمول سفارشی',
            default                  => $t->formula_type,
        };
    }
}
