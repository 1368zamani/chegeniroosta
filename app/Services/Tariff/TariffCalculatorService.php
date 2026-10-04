<?php

namespace App\Services\Tariff;

use App\Models\TariffItem;
use App\Models\TariffSet;
use App\Services\Tariff\Calculators\CalculationResult;
use App\Services\Tariff\Calculators\CalculatorRegistry;
use InvalidArgumentException;

class TariffCalculatorService
{
    public function __construct(private CalculatorRegistry $registry) {}

    public function registry(): CalculatorRegistry
    {
        return $this->registry;
    }

    /**
     * محاسبه یک عنوان مشخص از یک مجموعه.
     */
    public function calculateItem(TariffItem $item, array $inputs): CalculationResult
    {
        $key = $item->title?->formula_key;
        if (!$key) {
            throw new InvalidArgumentException('این عنوان فرمول محاسبه‌پذیر ندارد.');
        }

        $calculator = $this->registry->get($key);
        if (!$calculator) {
            throw new InvalidArgumentException("فرمول «$key» پیاده‌سازی نشده است.");
        }

        return $calculator->calculate($item, $inputs, $item->set);
    }

    /**
     * ساختار ورودی‌های فرم برای یک عنوان.
     */
    public function inputSchema(TariffItem $item): array
    {
        $key = $item->title?->formula_key;
        if (!$key) return [];
        $calc = $this->registry->get($key);
        return $calc ? $calc->inputSchema($item) : [];
    }

    /**
     * آیا این عنوان محاسبه‌پذیر است؟
     */
    public function isCalculable(TariffItem $item): bool
    {
        $key = $item->title?->formula_key;
        return $key && $this->registry->has($key);
    }
}
