<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * قرارداد هر calculator.
 * پارامترهای ورودی همیشه associative array از فرم public.
 */
interface TariffCalculator
{
    /** کلید یکتا که با tariff_titles.formula_key تطبیق داده می‌شود */
    public function key(): string;

    /** برچسب انسانی برای UI */
    public function label(): string;

    /** فیلدهای ورودی که در UI نمایش داده می‌شوند */
    public function inputSchema(): array;

    /** محاسبه */
    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult;
}
