<?php

namespace App\Services\Tariff\Calculators;

/**
 * نتیجه‌ی محاسبه‌ی یک عنوان.
 * همه‌ی مبالغ به ریال (int) ذخیره می‌شوند.
 */
class CalculationResult
{
    public function __construct(
        public int $amount = 0,
        public array $breakdown = [],
        public array $warnings = [],
        public array $meta = [],
    ) {}

    public static function make(int $amount, array $breakdown = [], array $warnings = [], array $meta = []): self
    {
        return new self($amount, $breakdown, $warnings, $meta);
    }

    public function toArray(): array
    {
        return [
            'amount'    => $this->amount,
            'breakdown' => $this->breakdown,
            'warnings'  => $this->warnings,
            'meta'      => $this->meta,
        ];
    }
}
