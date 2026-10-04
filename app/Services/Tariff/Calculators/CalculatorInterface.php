<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

interface CalculatorInterface
{
    /** کلید یکتا مطابق TariffTitle.formula_key */
    public function key(): string;

    /** برچسب فارسی */
    public function label(): string;

    /**
     * ساختار ورودی‌های مورد نیاز (برای فرم عمومی).
     *
     * @return array<int, array{key: string, label: string, type: string, unit?: string, required?: bool, options?: array, default?: mixed}>
     */
    public function inputSchema(TariffItem $item): array;

    /**
     * محاسبه با ورودی‌های کاربر.
     */
    public function calculate(TariffItem $item, array $inputs, ?TariffSet $set = null): CalculationResult;
}
