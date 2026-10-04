<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;
use InvalidArgumentException;

abstract class BaseCalculator implements CalculatorInterface
{
    /**
     * خواندن پارامتر از TariffItem.params (JSON).
     */
    protected function itemParam(TariffItem $item, string $key, $default = null)
    {
        $params = $item->params ?? [];
        return array_key_exists($key, $params) ? $params[$key] : $default;
    }

    /**
     * خواندن پارامتر سراسری از TariffSet->parameters.
     */
    protected function setParam(?TariffSet $set, string $key, $default = null)
    {
        if (!$set) return $default;
        $p = $set->parameters->firstWhere('key', $key);
        if (!$p) return $default;
        return $p->typedValue() ?? $default;
    }

    /**
     * خواندن ورودی کاربر.
     */
    protected function input(array $inputs, string $key, $default = null)
    {
        return array_key_exists($key, $inputs) ? $inputs[$key] : $default;
    }

    /**
     * ورودی عددی الزامی (غیرمنفی).
     */
    protected function requiredNumber(array $inputs, string $key, string $label): float
    {
        $v = $this->input($inputs, $key);
        if ($v === null || $v === '') {
            throw new InvalidArgumentException("فیلد «{$label}» الزامی است.");
        }
        if (!is_numeric($v)) {
            throw new InvalidArgumentException("فیلد «{$label}» باید عدد باشد.");
        }
        $f = (float) $v;
        if ($f < 0) {
            throw new InvalidArgumentException("فیلد «{$label}» نمی‌تواند منفی باشد.");
        }
        return $f;
    }

    /**
     * ورودی گزینه‌ای (select).
     */
    protected function requiredChoice(array $inputs, string $key, array $allowed, string $label): string
    {
        $v = (string) $this->input($inputs, $key, '');
        if ($v === '') {
            throw new InvalidArgumentException("فیلد «{$label}» الزامی است.");
        }
        if (!in_array($v, $allowed, true)) {
            throw new InvalidArgumentException("مقدار «{$label}» نامعتبر است.");
        }
        return $v;
    }

    protected function step(string $label, int $amount, ?string $detail = null): array
    {
        return [
            'label'  => $label,
            'detail' => $detail,
            'amount' => $amount,
        ];
    }

    protected function toInt(float $v): int
    {
        return (int) round($v);
    }
}
