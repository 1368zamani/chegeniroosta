<?php

namespace Database\Seeders;

use App\Models\TariffTitle;
use Illuminate\Database\Seeder;

/**
 * کاتالوگ رسمی عناوین عوارض و بهای خدمات دهیاری
 *
 * مبنا: دستورالعمل وزارت کشور موضوع تبصره ۱ ماده ۲
 * قانون درآمد پایدار و هزینه شهرداری‌ها و دهیاری‌ها
 * (نامه ۱۸۴۱۲۵ مورخ ۱۴۰۴/۰۹/۲۹، اجرا از ۱۴۰۵/۰۱/۰۱)
 *
 * ⚠️ عناوین مخصوص شهرداری در این کاتالوگ نیستند:
 *    ریلی، تردد شبانه کامیون، سازه‌های ساحلی،
 *    مسافربری اینترنتی، ماده ۱۰۰
 */
class TariffCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // ═══ عوارض محلی (levy) — ۱۳ عنوان اصلی ═══
        $levies = [
            ['L01', 'عوارض سالیانه املاک و مستقلات', 'annual_property',
                'سطح روستا. مبنا: ارزش معاملاتی ماده ۶۴ ق.م.م + ضریب تعدیل'],

            ['L02', 'عوارض تفکیک اراضی', 'land_subdivision',
                'تا سقف ۲۵٪ ارزش روز زمین. زمین آزادشده برای معابر کسر می‌شود'],

            ['L03', 'عوارض چشمه‌های معدنی', 'mineral_springs', null],

            ['L04', 'عوارض مجوز حصارکشی و دیوارکشی', 'fence_permit',
                'املاک فاقد مستحدثات. TODO(verify): نرخ هر نوع'],

            ['L05', 'عوارض زیربنا', 'sub_building',
                'مسکونی/غیرمسکونی/بالکن و پیش‌آمدگی. شامل احداث، اضافه بنا و مازاد تراکم'],

            ['L06', 'عوارض مستحدثات محوطه', 'yard_structures',
                'آلاچیق، پارکینگ مسقف، استخر'],

            ['L07', 'عوارض تمدید و تجدید پروانه ساختمانی', 'permit_renewal',
                'سال اول رایگان، دوم ۳٪، بعد ۲٪/سال تا سقف ۲۰٪'],

            ['L08', 'عوارض قطع درختان', 'tree_cutting',
                'محیط بن × نرخ + ۵ نهال جایگزین. بدون مجوز ۵ برابر'],

            ['L09', 'عوارض مشاغل دائم و موقت', 'businesses',
                'نرخ پایه صنف × ضریب معبر. مهلت تا پایان اردیبهشت'],

            ['L10', 'عوارض تأسیسات روستایی', 'rural_installations',
                'دکل، آنتن، ترانسفورماتور. یک‌بار هنگام صدور مجوز'],

            ['L11', 'عوارض تابلوی تبلیغات محیطی', 'billboard',
                'تابلوی معرف سردرب معاف است'],

            ['L12', 'عوارض ارزش افزوده الحاق به بافت', 'value_added_zoning',
                'در اجرای طرح هادی. حداکثر ۳۰٪ ارزش افزوده'],

            ['L13', 'عوارض ارزش افزوده طرح‌های عمران روستایی', 'value_added_development',
                'حق مشرفیت. حداکثر ۳۰٪'],
        ];
        $this->seedTitles($levies, 'levy', false, 1);

        // ═══ عوارض اختیاری (فقط با تصویب شورا) ═══
        $optional = [
            ['L14', 'عوارض جایگاه‌های سوخت', 'fuel_stations', 'ماهانه، هر نازل. فقط با تصویب شورا'],
            ['L15', 'عوارض انبار کالا', 'warehouses', null],
            ['L16', 'عوارض دستفروشان', 'street_vendors', null],
            ['L17', 'عوارض باسکول‌داران', 'weighbridge', null],
            ['L18', 'عوارض قراردادهای پیمانکاری', 'contractor_contracts', '۵٪ مبلغ قرارداد'],
        ];
        $this->seedTitles($optional, 'levy', true, 14);

        // ═══ بهای خدمات (service_fee) — ۱۷ عنوان ═══
        $services = [
            ['S01', 'بهای خدمات کارشناسی/فنی/آموزشی (داخل محدوده)', 'technical_in', null],
            ['S02', 'بهای خدمات کارشناسی/فنی/آموزشی (خارج محدوده)', 'technical_out', null],
            ['S03', 'بهای خدمات ایمنی و آتش‌نشانی (معبر بیش از ۸ متر)', 'fire_safety_wide', null],
            ['S04', 'بهای خدمات ایمنی و آتش‌نشانی (معبر کمتر از ۸ متر)', 'fire_safety_narrow', null],
            ['S05', 'بهای خدمات پارکینگ عمومی', 'public_parking', null],
            ['S06', 'بهای خدمات آرامستان (غسالخانه + قبر)', 'cemetery', 'نرخ غسالخانه و قبر جداگانه'],
            ['S07', 'بهای خدمات ماشین‌آلات (ساعتی)', 'machinery_hourly', 'نرخ ساعتی × ساعت کار'],
            ['S08', 'بهای خدمات ماشین‌آلات (سرویسی)', 'machinery_service', 'نرخ ثابت هر سرویس'],
            ['S09', 'بهای خدمات بهره‌برداری از معابر و فضای عمومی', 'public_space_use', null],
            ['S10', 'بهای خدمات مجوز حفاری/لکه‌گیری/ترمیم آسفالت', 'excavation', 'متر طول بر اساس نوع معبر و عرض'],
            ['S11', 'بهای خدمات مراکز رفاهی/تفریحی/فرهنگی/ورزشی', 'recreation_centers', 'شامل ورودی روستاهای گردشگری'],
            ['S12', 'بهای خدمات پسماند مسکونی (سالانه)', 'waste_residential', null],
            ['S13', 'بهای خدمات پسماند تجاری-اداری (سالانه)', 'waste_commercial', null],
            ['S14', 'بهای خدمات پسماند صنعتی (سالانه)', 'waste_industrial', null],
            ['S15', 'بهای خدمات گواهی‌ها و مجوزها', 'certificates', null],
            ['S16', 'بهای خدمات شناسنامه ساختمانی المثنی', 'building_id_duplicate', null],
            ['S17', 'بهای خدمات حمام عمومی', 'public_bath', null],
        ];
        $this->seedTitles($services, 'service_fee', false, 19);

        // ═══ جریمه (penalty) ═══
        TariffTitle::updateOrCreate(
            ['code' => 'P01'],
            [
                'type'           => 'penalty',
                'name_fa'        => 'جریمه تخلفات ساختمانی کمیسیون ماده ۹۹',
                'formula_key'    => 'article_99_penalty',
                'statutory_note' => 'غیرقابل تقسیط. نرخ بر اساس نوع بنا، طبقه و مرحله ساخت. TODO(verify)',
                'is_optional'    => false,
                'sort_order'     => 99,
                'is_active'      => true,
            ]
        );

        // ═══ سایر خدمات (اختیاری، فرمول آزاد) ═══
        TariffTitle::updateOrCreate(
            ['code' => 'X01'],
            [
                'type'           => 'service_fee',
                'name_fa'        => 'سایر خدمات قابل تعریف توسط شورا',
                'formula_key'    => 'custom',
                'statutory_note' => 'فقط با تصویب شورای اسلامی روستا',
                'is_optional'    => true,
                'sort_order'     => 100,
                'is_active'      => true,
            ]
        );

        // ═══ گزارش ═══
        $this->command->info('✅ کاتالوگ ساخته شد: ' . TariffTitle::count() . ' عنوان');
        $this->command->info('   عوارض (levy):         ' . TariffTitle::where('type', 'levy')->count());
        $this->command->info('   بهای خدمات (service): ' . TariffTitle::where('type', 'service_fee')->count());
        $this->command->info('   جریمه (penalty):      ' . TariffTitle::where('type', 'penalty')->count());
        $this->command->info('   اختیاری:              ' . TariffTitle::where('is_optional', true)->count());
    }

    private function seedTitles(array $rows, string $type, bool $optional, int $startOrder): void
    {
        foreach ($rows as $i => $row) {
            [$code, $nameFa, $formulaKey] = $row;
            $note = $row[3] ?? null;

            TariffTitle::updateOrCreate(
                ['code' => $code],
                [
                    'type'           => $type,
                    'name_fa'        => $nameFa,
                    'formula_key'    => $formulaKey,
                    'statutory_note' => $note,
                    'is_optional'    => $optional,
                    'sort_order'     => $startOrder + $i,
                    'is_active'      => true,
                ]
            );
        }
    }
}
