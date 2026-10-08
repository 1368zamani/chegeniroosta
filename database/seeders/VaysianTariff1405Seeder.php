<?php

namespace Database\Seeders;

use App\Models\TariffSet;
use App\Models\TariffTitle;
use App\Models\TariffParameter;
use App\Models\TariffItem;
use App\Models\TariffExemption;
use App\Models\Village;
use Illuminate\Database\Seeder;

class VaysianTariff1405Seeder extends Seeder
{
    /**
     * مقادیر پیش‌فرض (قابل ویرایش بعد از درج توسط کارشناس)
     */
    private array $defaults = [
        'land_value_per_m2'      => 50000000,
        'building_value_per_m2'  => 20000000,
        'adjustment_coefficient' => 0.115,
        'district_coefficient'   => 1,
        'inflation_rate'         => 0.35,
        'jalali_year'            => 1405,
    ];

    public function run(): void
    {
        $villages = Village::orderBy('id')->get();
        $total    = $villages->count();
        $created  = 0;
        $skipped  = 0;

        $this->command->info("شروع ساخت ست تعرفه برای {$total} روستا...");
        $this->command->info(str_repeat('─', 60));

        foreach ($villages as $index => $village) {
            try {
                $set = TariffSet::updateOrCreate(
                    [
                        'village_id'  => $village->id,
                        'jalali_year' => $this->defaults['jalali_year'],
                    ],
                    [
                        'inflation_rate' => $this->defaults['inflation_rate'],
                        'status'         => 'draft',
                        'created_by'     => 1,
                    ]
                );

                $this->seedParameters($set);
                $this->seedBuildingPermit($set);
                $this->seedFence($set);
                $this->seedYardStructures($set);
                $this->seedSubdivision($set);
                $this->seedValueAdded($set);
                $this->seedBusinesses($set);
                $this->seedServices($set);
                $this->seedExemptions($set);

                $created++;
                $num = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
                $this->command->info("✅ [{$num}/{$total}] {$village->name} (Set ID: {$set->id}) — آیتم‌ها: {$set->items()->count()}");
            } catch (\Throwable $e) {
                $skipped++;
                $this->command->warn("⚠️  {$village->name}: " . $e->getMessage());
            }
        }

        $this->command->info(str_repeat('─', 60));
        $this->command->info("پایان: {$created} روستا موفق، {$skipped} خطا");
    }

    private function seedParameters(TariffSet $set): void
    {
        TariffParameter::put($set, 'land_value_per_m2',
            $this->defaults['land_value_per_m2'], 'ریال/م²', 'P - قیمت منطقه‌ای املاک');
        TariffParameter::put($set, 'building_value_per_m2',
            $this->defaults['building_value_per_m2'], 'ریال/م²', 'ارزش زیربنا');
        TariffParameter::put($set, 'adjustment_coefficient',
            $this->defaults['adjustment_coefficient'], 'ضریب', 'P1 - ضریب تعدیل هیئت وزیران');
        TariffParameter::put($set, 'district_coefficient',
            $this->defaults['district_coefficient'], 'ضریب', 'B - ضریب شورای بخش');
        TariffParameter::put($set, 'inflation_rate',
            $this->defaults['inflation_rate'], 'درصد', 'سقف افزایش سالانه');
    }

    private function seedBuildingPermit(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'L05', [
            ['مسکونی تک‌واحدی - همکف/پیلوت',   220, 'ضریب', 'P × P1 × M × B'],
            ['مسکونی تک‌واحدی - زیرزمین',      180, 'ضریب', 'P × P1 × M × B'],
            ['مسکونی تک‌واحدی - طبقه ۳+',      190, 'ضریب', 'P × P1 × M × B'],
            ['مجتمع مسکونی - همکف',            190, 'ضریب', 'P × P1 × M × B'],
            ['مجتمع مسکونی - زیرزمین',         160, 'ضریب', 'P × P1 × M × B'],
            ['مجتمع مسکونی - طبقه ۳+',         170, 'ضریب', 'P × P1 × M × B'],
            ['تجاری - همکف',                   290, 'ضریب', 'P × P1 × M × B'],
            ['تجاری - زیرزمین',                250, 'ضریب', 'P × P1 × M × B'],
            ['تجاری - طبقه ۳+',                230, 'ضریب', 'P × P1 × M × B'],
            ['اداری/گردشگری - همکف',           250, 'ضریب', 'P × P1 × M × B'],
            ['اداری/گردشگری - زیرزمین',        250, 'ضریب', 'P × P1 × M × B'],
            ['اداری/گردشگری - طبقه ۳+',        240, 'ضریب', 'P × P1 × M × B'],
            ['صنعتی - همکف',                   190, 'ضریب', 'P × P1 × M × B'],
            ['صنعتی - زیرزمین',                160, 'ضریب', 'P × P1 × M × B'],
            ['صنعتی - طبقه ۳+',                180, 'ضریب', 'P × P1 × M × B'],
            ['کشاورزی - همکف',                 140, 'ضریب', 'P × P1 × M × B'],
            ['کشاورزی - زیرزمین',              120, 'ضریب', 'P × P1 × M × B'],
            ['کشاورزی - طبقه ۳+',              120, 'ضریب', 'P × P1 × M × B'],
            ['خدماتی/آموزشی/ورزشی - همکف',     160, 'ضریب', 'P × P1 × M × B'],
            ['خدماتی/آموزشی/ورزشی - زیرزمین',  150, 'ضریب', 'P × P1 × M × B'],
            ['خدماتی/آموزشی/ورزشی - طبقه ۳+',  140, 'ضریب', 'P × P1 × M × B'],
            ['دام/انباری - همکف',              30,  'ضریب', 'P × P1 × M × B'],
            ['دام/انباری - زیرزمین',           25,  'ضریب', 'P × P1 × M × B'],
            ['دام/انباری - طبقه ۳+',           20,  'ضریب', 'P × P1 × M × B'],
        ]);
    }

    private function seedFence(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'L04', [
            ['دیوار آجری تا ۱.۵ متر',     180, 'متر طول', 'B × P × L × P1'],
            ['دیوار آجری بیش از ۱.۵ متر', 190, 'متر طول', 'B × P × L × P1'],
            ['فنس / فلزی / نرده',         110, 'متر طول', 'B × P × L × P1'],
        ]);
    }

    private function seedYardStructures(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'L06', [
            ['بالکن سه‌طرف بسته',      170,  'مترمربع', 'P × P1 × M × B'],
            ['تراس/بالکن دوطرف باز',   140,  'مترمربع', 'P × P1 × M × B'],
            ['آلاچیق و سایه‌بان ثابت',  3500, 'مترمربع', 'P × P1 × M × (B/100)'],
            ['استخر روباز/مسقف',       5000, 'مترمربع', 'P × P1 × M × (B/100)'],
            ['پارکینگ مسقف',          3000, 'مترمربع', 'P × P1 × M × (B/100)'],
        ]);
    }

    private function seedSubdivision(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'L02', [
            ['تفکیک تا ۵۰۰ م²',          12, 'درصد از ارزش افزوده', 'value_added × 0.12'],
            ['تفکیک ۵۰۰ تا ۱۰,۰۰۰ م²',   15, 'درصد از ارزش افزوده', 'value_added × 0.15'],
            ['تفکیک بالای ۱۰,۰۰۰ م²',    20, 'درصد از ارزش افزوده', 'value_added × 0.20'],
        ]);
    }

    private function seedValueAdded(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'L12', [
            ['ارزش افزوده الحاق به بافت', 30, 'درصد', 'value_added × 0.30'],
        ]);
        $this->seedTitleItems($set, 'L13', [
            ['ارزش افزوده تغییر کاربری', 30, 'درصد', 'value_added × 0.30'],
            ['حق مشرفیت (تعریض معابر)',  25, 'درصد', 'value_added × 0.25'],
        ]);
    }

    private function seedBusinesses(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'L09', [
            ['نانوایی و خبازی',                15000000, 'ریال/سال', 'ثابت'],
            ['خواروبار، سوپرمارکت و لبنیات',   15000000, 'ریال/سال', 'ثابت'],
            ['رستوران، غذاخوری و قصابی',       12000000, 'ریال/سال', 'ثابت'],
            ['جایگاه سوخت و پمپ بنزین',        12000000, 'ریال/سال', 'ثابت + ۲۷۰,۰۰۰/نازل'],
            ['تعمیرگاه خودرو و کارواش',         6000000, 'ریال/سال', 'ثابت'],
            ['مصالح‌فروشی و ابزارآلات',         10000000, 'ریال/سال', 'ثابت'],
            ['نجاری، درودگری و کارگاه فلزی',    10000000, 'ریال/سال', 'ثابت'],
            ['فروشگاه لوازم خانگی و صنایع دستی',15000000, 'ریال/سال', 'ثابت'],
            ['کمباین و تراکتور سنگین',          15000000, 'ریال/سال', 'ثابت/دستگاه'],
            ['تالار پذیرایی و مجتمع تفریحی',    20000000, 'ریال/سال', 'ثابت'],
            ['فروش سموم و خدمات کشاورزی',       7000000,  'ریال/سال', 'ثابت'],
            ['سایر مشاغل پیش‌بینی‌نشده',         8000000,  'ریال/سال', 'ثابت'],
        ]);
    }

    private function seedServices(TariffSet $set): void
    {
        $this->seedTitleItems($set, 'S12', [['پسماند منازل مسکونی', 4500000, 'ریال/سال', 'ثابت']]);
        $this->seedTitleItems($set, 'S13', [['پسماند واحدهای تجاری و اداری', 7000000, 'ریال/سال', 'ثابت']]);
        $this->seedTitleItems($set, 'S06', [
            ['آرامستان - شستشو و غسالخانه', 6500000, 'ریال/متوفی', 'ثابت'],
            ['آرامستان - مجوز دفن/قبر',      6500000, 'ریال/متوفی', 'ثابت'],
        ]);
        $this->seedTitleItems($set, 'S01', [['کارشناسی فنی و بازدید ملک', 4000000, 'ریال/مورد', 'ثابت']]);
        $this->seedTitleItems($set, 'S15', [['صدور انواع گواهی‌ها و استعلامات', 18000000, 'ریال/گواهی', 'ثابت']]);
        $this->seedTitleItems($set, 'S16', [['شناسنامه ساختمانی المثنی', 25000000, 'ریال/نسخه', 'ثابت']]);
        $this->seedTitleItems($set, 'S07', [['بیل مکانیکی/لودر (ساعتی)', 25000000, 'ریال/ساعت', 'ثابت']]);
        $this->seedTitleItems($set, 'S08', [['کامیون دهیاری (شعاع ۲۰ کیلومتر)', 20000000, 'ریال/سرویس', 'ثابت']]);
        $this->seedTitleItems($set, 'S10', [['خسارت حفاری معابر آسفالت', 12000000, 'ریال/متر', 'ثابت']]);
        $this->seedTitleItems($set, 'S11', [['ورودی مراکز تفریحی و گردشگری', 60000, 'ریال/خودرو', 'ثابت']]);
    }

    private function seedExemptions(TariffSet $set): void
    {
        $titleL05 = TariffTitle::where('code', 'L05')->first();
        if (!$titleL05) return;

        $exemptions = [
            ['مددجویان',     100, 'مددجویان کمیته امداد و بهزیستی',  'یک‌بار تا سقف ۷۵ مترمربع'],
            ['ایثارگران',    100, 'ایثارگران و خانواده شهدا',        'یک واحد مسکونی تا ۱۲۰m'],
            ['جوانی جمعیت',   10, 'خانواده دارای ۳ فرزند زیر ۲۰ سال', 'قانون جوانی جمعیت'],
            ['اماکن مذهبی',  100, 'مساجد، حسینیه‌ها و اماکن مذهبی',   'تأییدیه سازمان تبلیغات'],
        ];

        foreach ($exemptions as $row) {
            TariffExemption::updateOrCreate(
                [
                    'tariff_set_id'   => $set->id,
                    'tariff_title_id' => $titleL05->id,
                    'kind'            => $row[0],
                ],
                [
                    'discount_percent' => $row[1],
                    'description'      => $row[2],
                    'statutory_note'   => $row[3],
                    'is_active'        => true,
                ]
            );
        }
    }

    private function seedTitleItems(TariffSet $set, string $titleCode, array $rows): void
    {
        $title = TariffTitle::where('code', $titleCode)->first();
        if (!$title) return;

        foreach ($rows as $row) {
            $description = $row[0] ?? '';
            $value       = $row[1] ?? 0;
            $unit        = $row[2] ?? 'ریال';
            $formula     = $row[3] ?? '';

            TariffItem::updateOrCreate(
                [
                    'tariff_set_id'   => $set->id,
                    'tariff_title_id' => $title->id,
                    'notes'           => $description,
                ],
                [
                    'is_active'        => true,
                    'previous_amount'  => 0,
                    'increase_percent' => 0,
                    'params'           => json_encode(['B' => $value, 'value' => $value], JSON_UNESCAPED_UNICODE),
                    'final_amount'     => is_numeric($value) ? (int) $value : 0,
                    'unit'             => $unit,
                    'formula_display'  => $formula,
                ]
            );
        }
    }
}
