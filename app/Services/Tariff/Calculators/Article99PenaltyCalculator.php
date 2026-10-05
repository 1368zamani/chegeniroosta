<?php

namespace App\Services\Tariff\Calculators;

use App\Models\TariffItem;
use App\Models\TariffSet;

/**
 * جریمه تخلفات ساختمانی کمیسیون ماده ۹۹.
 *
 * ⚠️ TODO(verify): فرمول دقیق در متن OCR نبود.
 * این یک ساختار قابل‌تغییر است: ضرایب از params آیتم خوانده می‌شوند.
 * اگر فرمول رسمی متفاوت است، فقط همین کلاس باید اصلاح شود.
 *
 * این جریمه غیرقابل تقسیط است و در محاسبه‌ی تقسیط نباید وارد شود.
 */
class Article99PenaltyCalculator implements TariffCalculator
{
    public function key(): string { return 'article_99_penalty'; }
    public function label(): string { return 'جریمه تخلفات ساختمانی ماده ۹۹'; }

    public function inputSchema(): array
    {
        return [
            'base_value' => ['label' => 'ارزش پایه‌ی ملک (ریال)', 'type' => 'number', 'required' => true],
            'building_type' => [
                'label' => 'نوع بنا',
                'type'  => 'select',
                'options' => [
                    'residential' => 'مسکونی',
                    'commercial'  => 'تجاری',
                    'industrial'  => 'صنعتی',
                    'other'       => 'سایر',
                ],
                'default' => 'residential',
            ],
            'floors' => ['label' => 'تعداد طبقات', 'type' => 'number', 'default' => 1, 'min' => 1, 'max' => 20],
            'stage' => [
                'label' => 'مرحله‌ی ساخت',
                'type'  => 'select',
                'options' => [
                    'foundation' => 'فونداسیون',
                    'skeleton'   => 'اسکلت',
                    'roof'       => 'سقف',
                    'finishing'  => 'نازک‌کاری',
                    'completed'  => 'تکمیل',
                ],
                'default' => 'skeleton',
            ],
        ];
    }

    public function calculate(TariffSet $set, TariffItem $item, array $inputs): CalculationResult
    {
        $baseValue = (float) ($inputs['base_value'] ?? 0);
        $type      = (string) ($inputs['building_type'] ?? 'residential');
        $floors    = max(1, min(20, (int) ($inputs['floors'] ?? 1)));
        $stage     = (string) ($inputs['stage'] ?? 'skeleton');

        $typeFactors = $item->param('type_factors') ?? [
            'residential' => 1.0,
            'commercial'  => 2.0,
            'industrial'  => 1.5,
            'other'       => 1.0,
        ];
        $stageFactors = $item->param('stage_factors') ?? [
            'foundation' => 0.25,
            'skeleton'   => 0.50,
            'roof'       => 0.75,
            'finishing'  => 0.90,
            'completed'  => 1.00,
        ];

        if (is_string($typeFactors))  $typeFactors  = json_decode($typeFactors, true) ?: [];
        if (is_string($stageFactors)) $stageFactors = json_decode($stageFactors, true) ?: [];

        $tFactor = (float) ($typeFactors[$type] ?? 1.0);
        $sFactor = (float) ($stageFactors[$stage] ?? 0.5);

        // TODO(verify): فرمول دقیق ماده ۹۹ — ساختار موقت:
        //   جریمه = ارزش پایه × ضریب نوع × ضریب مرحله × تعداد طبقات
        $amount = (int) round($baseValue * $tFactor * $sFactor * $floors);

        return CalculationResult::make($amount, [
            'ارزش پایه'      => $baseValue,
            'نوع بنا'        => $type,
            'ضریب نوع'       => $tFactor,
            'تعداد طبقات'    => $floors,
            'مرحله‌ی ساخت'   => $stage,
            'ضریب مرحله'     => $sFactor,
        ], [
            '⚠️ TODO(verify): فرمول دقیق ماده ۹۹ در متن مرجع نبود — ساختار فعلی نمونه است.',
            '⚠️ این جریمه غیرقابل تقسیط است (ماده ۹۹).',
        ]);
    }
}
