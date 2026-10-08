<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * کلاس پایه برای Calculator های جدید
 * — سازگار با TariffCalculator قدیمی
 * — متد type() رو پیش‌فرض levy می‌کنه
 */
abstract class BaseCalculator implements TariffCalculator
{
    /**
     * پیش‌فرض: levy
     * کلاس‌های زیر می‌توانند override کنند
     */
    public function type(): string
    {
        return 'levy';
    }

    protected function params(TariffSet $set): array
    {
        return $set->parameters()
            ->pluck('value', 'name')
            ->toArray();
    }

    protected function param(TariffSet $set, string $name, $default = 0)
    {
        return $this->params($set)[$name] ?? $default;
    }

    protected function baseAmount(TariffItem $item): float
    {
        return (float) ($item->final_amount ?: $item->previous_amount ?: 0);
    }
}
