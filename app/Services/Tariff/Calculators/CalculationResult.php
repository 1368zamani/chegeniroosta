<?php

namespace App\Services\Tariff\Calculators;

/**
 * نتیجه محاسبه یک عنوان تعرفه.
 * تمام مبالغ به ریال و bigint.
 */
class CalculationResult
{
    public function __construct(
        public int $total,
        public array $steps = [],
        public array $meta = [],
    ) {}

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'steps' => $this->steps,
            'meta'  => $this->meta,
        ];
    }
}
