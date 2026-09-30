<?php

namespace Database\Seeders;

use App\Models\Dehyari;
use App\Models\FeeCategory;
use App\Models\FeeParameter;
use App\Models\FeeTariff;
use App\Models\FeeType;
use App\Models\PaymentAccount;
use App\Models\Village;
use Illuminate\Database\Seeder;

/**
 * داده نمونه برای فعال شدن محاسبه‌گر.
 * ⚠️ همه اعداد «نمونه» هستند و باید بعداً با مصوبه شورا جایگزین شوند.
 */
class FeeDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ─────── ۱. دهیاری برای هر روستا (اگر نیست) ───────
        foreach (Village::all() as $v) {
            Dehyari::firstOrCreate(
                ['village_id' => $v->id],
                ['name' => 'دهیاری ' . $v->name, 'is_active' => true]
            );
        }

        // ─────── ۲. پارامترها برای انواع عوارض ───────
        $this->seedParameters();

        // ─────── ۳. تعرفه نمونه برای هر دهیاری × هر نوع ───────
        $this->seedTariffs();

        // ─────── ۴. حساب بانکی نمونه ───────
        $this->seedAccounts();

        $this->command->info('✓ داده نمونه ساخته شد (پارامترها، تعرفه‌ها، حساب‌ها)');
    }

    private function seedParameters(): void
    {
        // نقشه: نام نوع عوارض => آرایه پارامترها
        $map = [
            'احداث بنا' => [
                ['area',   'زیربنا',                'number', 'مترمربع', true],
                ['floors', 'تعداد طبقات',           'number', 'طبقه',    true],
            ],
            'توسعه بنا' => [
                ['area',   'زیربنا',                'number', 'مترمربع', true],
                ['floors', 'تعداد طبقات',           'number', 'طبقه',    true],
            ],
            'تجدید بنا' => [
                ['area',   'زیربنا',                'number', 'مترمربع', true],
                ['floors', 'تعداد طبقات',           'number', 'طبقه',    true],
            ],
            'تعمیرات ساختمانی' => [
                ['area',   'زیربنا',                'number', 'مترمربع', true],
            ],
            'حصارکشی' => [
                ['length', 'طول حصار',              'number', 'متر',     true],
                ['height', 'ارتفاع حصار',           'number', 'متر',     true],
            ],
            'سایر خدمات ساخت‌وساز' => [
                ['area',   'زیربنا',                'number', 'مترمربع', true],
            ],
            'پسماند خانوار' => [
                ['units',  'تعداد خانوار',          'number', 'خانوار',  true],
            ],
            'پسماند واحد تجاری' => [
                ['units',  'تعداد واحد تجاری',      'number', 'واحد',    true],
            ],
            'پسماند واحد اداری' => [
                ['units',  'تعداد واحد اداری',      'number', 'واحد',    true],
            ],
            'واحد صنفی / مغازه' => [
                ['area',   'متراژ مغازه',           'number', 'مترمربع', true],
            ],
            'کارگاه' => [
                ['area',   'متراژ کارگاه',          'number', 'مترمربع', true],
            ],
            'واحد خدماتی' => [
                ['area',   'متراژ واحد',            'number', 'مترمربع', true],
            ],
            'سایر فعالیت‌ها' => [
                ['area',   'متراژ',                 'number', 'مترمربع', true],
            ],
        ];

        foreach ($map as $typeName => $params) {
            $type = FeeType::where('name', $typeName)->first();
            if (!$type) continue;

            foreach ($params as $i => [$key, $label, $inputType, $unitLabel, $required]) {
                FeeParameter::updateOrCreate(
                    ['fee_type_id' => $type->id, 'key' => $key],
                    [
                        'label'         => $label,
                        'input_type'    => $inputType,
                        'unit_label'    => $unitLabel,
                        'is_required'   => $required,
                        'is_multiplier' => false,
                        'sort_order'    => $i,
                    ]
                );
            }
        }
    }

    private function seedTariffs(): void
    {
        $year = (int) \Morilog\Jalali\Jalalian::now()->getYear(); // سال شمسی

        // نقشه: نوع عوارض => [نرخ هر واحد (ریال), فرمول]
        $map = [
            'احداث بنا'                => [500_000,   'area_x_rate'],
            'توسعه بنا'                => [400_000,   'area_x_rate'],
            'تجدید بنا'                => [300_000,   'area_x_rate'],
            'تعمیرات ساختمانی'         => [200_000,   'area_x_rate'],
            'حصارکشی'                  => [150_000,   'area_x_rate'],
            'سایر خدمات ساخت‌وساز'     => [100_000,   'area_x_rate'],
            'پسماند خانوار'            => [600_000,   'units_x_rate'],
            'پسماند واحد تجاری'        => [1_200_000, 'units_x_rate'],
            'پسماند واحد اداری'        => [1_000_000, 'units_x_rate'],
            'واحد صنفی / مغازه'        => [800_000,   'area_x_rate'],
            'کارگاه'                   => [500_000,   'area_x_rate'],
            'واحد خدماتی'              => [400_000,   'area_x_rate'],
            'سایر فعالیت‌ها'           => [300_000,   'area_x_rate'],
        ];

        foreach (Dehyari::all() as $dehyari) {
            foreach ($map as $typeName => [$rate, $formula]) {
                $type = FeeType::where('name', $typeName)->first();
                if (!$type) continue;

                FeeTariff::updateOrCreate(
                    [
                        'dehyari_id'  => $dehyari->id,
                        'fee_type_id' => $type->id,
                        'year'        => $year,
                    ],
                    [
                        'base_amount'       => 0,
                        'unit_rate'         => $rate,
                        'coefficient'       => 1.0000,
                        'formula_type'      => $formula,
                        'unit'              => 'rial',
                        'is_active'         => true,
                        'description'       => '⚠️ نمونه — بعداً با مصوبه شورا جایگزین شود',
                    ]
                );
            }
        }
    }

    private function seedAccounts(): void
    {
        foreach (Dehyari::all() as $dehyari) {
            PaymentAccount::updateOrCreate(
                ['dehyari_id' => $dehyari->id, 'is_default' => true],
                [
                    'bank_name'      => 'بانک ملی ایران',
                    'account_number' => '020000000000000000000000',
                    'card_number'    => '6037-9900-0000-0000',
                    'sheba'          => 'IR120170000000000000000000',
                    'account_holder' => $dehyari->name,
                    'is_active'      => true,
                ]
            );
        }
    }
}
