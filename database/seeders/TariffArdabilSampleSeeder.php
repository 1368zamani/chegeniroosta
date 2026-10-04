<?php

namespace Database\Seeders;

use App\Models\TariffItem;
use App\Models\TariffParameter;
use App\Models\TariffSet;
use App\Models\Village;
use Illuminate\Database\Seeder;

/**
 * ⚠️⚠️⚠️ داده‌ی «نمونه» — نرخ رسمی چگنی نیست ⚠️⚠️⚠️
 *
 * این داده فقط برای آزمایش سیستم است و مبنای آن «نمونه‌ی اردبیل» است.
 * نرخ‌های واقعی باید توسط شورای اسلامی هر روستا تصویب و در پنل ادمین وارد شود.
 */
class TariffArdabilSampleSeeder extends Seeder
{
    public function run(): void
    {
        $village = Village::where('slug', 'abuzar')->first();
        if (!$village) {
            $this->command->error('روستای ابوذر پیدا نشد.');
            return;
        }

        $year = (int) \Morilog\Jalali\Jalalian::now()->getYear();

        // پاکسازی نمونه قبلی
        TariffSet::where('village_id', $village->id)->where('jalali_year', $year)->delete();

        $set = TariffSet::create([
            'village_id'     => $village->id,
            'jalali_year'    => $year,
            'inflation_rate' => 40.00,
            'status'         => 'draft',
            'approval_notes' => '⚠️ نمونه — نرخ رسمی نیست. مبنا: نمونه‌ی اردبیل.',
        ]);

        // ═══ پارامترهای سراسری (نمونه) ═══
        $params = [
            ['land_value_per_m2',        '2000000',    'ریال/مترمربع', 'ارزش هر متر زمین (نمونه)'],
            ['building_value_per_m2',    '8000000',    'ریال/مترمربع', 'ارزش هر متر بنا (نمونه)'],
            ['adjustment_coefficient',   '0.115',      'ضریب',         'ضریب تعدیل ۱۱٫۵٪ (متن سند)'],
            ['subbuilding_coefficient',  '0.115',      'ضریب',         'ضریب زیربنا'],
        ];
        foreach ($params as [$k, $v, $u, $d]) {
            TariffParameter::create([
                'tariff_set_id' => $set->id,
                'key'   => $k,
                'value' => $v,
                'unit'  => $u,
                'description' => $d,
            ]);
        }

        // ═══ مقادیر نمونه برای هر عنوان (نمونه اردبیل) ═══
        $sampleData = [
            // کد عنوان => [final_amount, unit, params]
            'L01' => [0,             'rial', [
                'usage_factor_residential' => 0.12,
                'usage_factor_commercial'  => 0.20,
                'usage_factor_industrial'  => 0.13,
                'usage_factor_office'      => 0.09,
                'usage_factor_public'      => 0.09,
                'usage_factor_tourism'     => 0.09,
                'usage_factor_sport'       => 0.09,
                'usage_factor_agriculture' => 0.07,
                'usage_factor_workshop'    => 0.07,
            ]],
            'L02' => [0, 'rial', []],
            'L03' => [5000000, 'rial', []],
            'L04' => [0, 'rial', [
                'type_rate_brick'    => 150000,
                'type_rate_concrete' => 200000,
                'type_rate_metal'    => 100000,
                'height_factor_upto_150' => 1.0,
                'height_factor_over_150' => 1.5,
            ]],
            'L05' => [0, 'rial', [
                'kind_factor_normal'       => 1.0,
                'kind_factor_extra_after'  => 1.2,
                'kind_factor_over_density' => 1.5,
                'kind_factor_balcony'      => 0.5,
            ]],
            'L06' => [3000000, 'rial', []],
            'L07' => [0, 'rial', [
                'first_year_free'    => true,
                'year2_percent'      => 3.0,
                'after_year_percent' => 2.0,
                'cap_percent'        => 20.0,
            ]],
            'L08' => [0, 'rial', ['rate_per_cm' => 50000]],
            'L09' => [0, 'rial', [
                'base_rate_general'  => 2000000,
                'base_rate_food'     => 3000000,
                'base_rate_clothing' => 2500000,
                'base_rate_services' => 1800000,
                'base_rate_repair'   => 1500000,
                'base_rate_other'    => 1000000,
                'street_factor_main' => 1.0,
                'street_factor_side' => 0.7,
                'delay_penalty_factor' => 1.025,
            ]],
            'L10' => [10000000, 'rial', []],
            'L11' => [2000000,  'rial', []],
            'L12' => [0, 'rial', ['value_added_cap_percent' => 30.0]],
            'L13' => [0, 'rial', ['value_added_cap_percent' => 30.0]],
            'L14' => [5000000, 'rial', []],
            'L15' => [3000000, 'rial', []],
            'L16' => [500000, 'rial', []],
            'L17' => [2000000, 'rial', []],
            'L18' => [0, 'rial', ['contractor_percent' => 5.0]],

            'S01' => [3000000, 'rial', []],
            'S02' => [5000000, 'rial', []],
            'S03' => [8000000, 'rial', []],
            'S04' => [10000000, 'rial', []],
            'S05' => [500000,  'rial', []],
            'S06' => [3000000,  'rial', []],
            'S07' => [0, 'rial', [
                'hourly_rate_loader'    => 2500000,
                'hourly_rate_excavator' => 3000000,
                'hourly_rate_truck'     => 1500000,
                'hourly_rate_grader'    => 2000000,
                'hourly_rate_roller'    => 1800000,
                'location_factor_inside' => 1.0,
                'location_factor_outside'=> 1.3,
            ]],
            'S08' => [5000000, 'rial', []],
            'S09' => [1000000, 'rial', []],
            'S10' => [0, 'rial', [
                'rate_asphalt'   => 800000,
                'rate_concrete'  => 700000,
                'rate_dirt'      => 400000,
                'rate_stone'     => 600000,
                'width_factor_upto_50' => 1.0,
                'width_factor_over_50' => 1.5,
            ]],
            'S11' => [500000,  'rial', []],
            'S12' => [0, 'rial', [
                'annual_rate_residential' => 600000,
                'annual_rate_commercial'  => 1200000,
                'annual_rate_industrial'  => 2400000,
                'kind_factor_residential' => 1.0,
                'kind_factor_commercial'  => 1.5,
                'kind_factor_industrial'  => 2.5,
            ]],
            'S13' => [0, 'rial', [
                'annual_rate_residential' => 600000,
                'annual_rate_commercial'  => 1200000,
                'annual_rate_industrial'  => 2400000,
                'kind_factor_residential' => 1.0,
                'kind_factor_commercial'  => 1.5,
                'kind_factor_industrial'  => 2.5,
            ]],
            'S14' => [0, 'rial', [
                'annual_rate_residential' => 600000,
                'annual_rate_commercial'  => 1200000,
                'annual_rate_industrial'  => 2400000,
                'kind_factor_residential' => 1.0,
                'kind_factor_commercial'  => 1.5,
                'kind_factor_industrial'  => 2.5,
            ]],
            'S15' => [500000,  'rial', []],
            'S16' => [300000,  'rial', []],
            'S17' => [200000,  'rial', []],

            'P01' => [0, 'rial', []],
            'X01' => [0, 'rial', []],
        ];

        $titles = \App\Models\TariffTitle::all()->keyBy('code');
        foreach ($sampleData as $code => [$amount, $unit, $params]) {
            $title = $titles->get($code);
            if (!$title) continue;

            TariffItem::create([
                'tariff_set_id'    => $set->id,
                'tariff_title_id'  => $title->id,
                'is_active'        => !$title->is_optional,
                'previous_amount'  => 0,
                'increase_percent' => 0,
                'final_amount'     => $amount,
                'unit'             => $unit,
                'params'           => $params,
                'notes'            => '⚠️ نمونه — نرخ رسمی نیست',
            ]);
        }

        $this->command->info('✅ نمونه اردبیل ساخته شد برای ' . $village->name . ' — سال ' . $year);
        $this->command->warn('⚠️  این داده «نمونه» است، نرخ رسمی چگنی نیست.');
    }
}
