<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * کاتالوگ عوارض «پسماند» و «ساخت‌وساز» — مقدار‌دهی اولیه.
 *
 * ⚠️ مبالغ زیر **نمونه** هستند و باید توسط کارشناس عمران
 * بر اساس مصوبه‌ی شورای اسلامی هر روستا ویرایش شوند.
 *
 * اجرا:  php artisan db:seed --class=WorksFeeCatalogSeeder
 * این Seeder چند بار قابل اجراست (updateOrInsert).
 */
class WorksFeeCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $effective = now()->startOfYear()->toDateString(); // ۱ فروردین همین سال

        /*
         * عناوین موجود (فقط ستون‌های دسته‌بندی به‌روز می‌شوند)
         * و عناوین جدید (وارد می‌شوند).
         */
        $catalog = [
            // ══════════ 🗑 عوارض پسماند ══════════
            [
                'code' => 'S12', 'existing' => true,
                'category' => 'waste', 'subcategory' => null,
                'unit' => 'واحد مسکونی / سال', 'base_amount' => 1_200_000,
                'description' => 'جمع‌آوری هفتگی زباله‌ی خانگی',
                'sort_order' => 101,
            ],
            [
                'code' => 'S13', 'existing' => true,
                'category' => 'waste', 'subcategory' => null,
                'unit' => 'واحد تجاری / سال', 'base_amount' => 3_500_000,
                'description' => 'مغازه، دفتر، مطب و واحدهای اداری',
                'sort_order' => 102,
            ],
            [
                'code' => 'S14', 'existing' => true,
                'category' => 'waste', 'subcategory' => null,
                'unit' => 'واحد صنعتی / سال', 'base_amount' => 8_000_000,
                'description' => 'کارگاه و واحد تولیدی',
                'sort_order' => 103,
            ],
            [
                'code' => 'W01', 'existing' => false,
                'name_fa' => 'پسماند ساختمانی (نخاله)',
                'type' => 'service_fee', 'formula_key' => 'waste_construction',
                'category' => 'waste', 'subcategory' => null,
                'unit' => 'متر مکعب', 'base_amount' => 450_000,
                'description' => 'حمل و دفع نخاله‌ی ساختمانی',
                'sort_order' => 104,
            ],
            [
                'code' => 'W02', 'existing' => false,
                'name_fa' => 'پسماند کشاورزی',
                'type' => 'service_fee', 'formula_key' => 'waste_agriculture',
                'category' => 'waste', 'subcategory' => null,
                'unit' => 'هکتار / سال', 'base_amount' => 800_000,
                'description' => 'ضایعات کشاورزی و دامی',
                'sort_order' => 105,
            ],

            // ══════════ 🏗 ساخت‌وساز جدید ══════════
            [
                'code' => 'L05', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'new_build',
                'unit' => 'متر مربع', 'base_amount' => 950_000,
                'description' => 'درصد بنای احداثی، طبق پروانه‌ی ساختمانی',
                'sort_order' => 201,
            ],
            [
                'code' => 'B01', 'existing' => false,
                'name_fa' => 'عوارض صدور پروانه ساختمانی',
                'type' => 'levy', 'formula_key' => 'building_permit',
                'category' => 'construction', 'subcategory' => 'new_build',
                'unit' => 'متر مربع', 'base_amount' => 650_000,
                'description' => 'هزینه‌ی بررسی و صدور پروانه‌ی ساخت',
                'sort_order' => 202,
            ],
            [
                'code' => 'L06', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'new_build',
                'unit' => 'متر مربع', 'base_amount' => 250_000,
                'description' => 'بناهای الحاقی محوطه: انبار، آلاچیق، سرایدار',
                'sort_order' => 203,
            ],
            [
                'code' => 'L02', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'new_build',
                'unit' => 'متر مربع', 'base_amount' => 1_100_000,
                'description' => 'تفکیک زمین به قطعات با ابعاد مشخص',
                'sort_order' => 204,
            ],
            [
                'code' => 'L04', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'new_build',
                'unit' => 'متر طول', 'base_amount' => 300_000,
                'description' => 'دیوار حریم و حصارکشی زمین',
                'sort_order' => 205,
            ],
            [
                'code' => 'L12', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'new_build',
                'unit' => 'متر مربع', 'base_amount' => 1_500_000,
                'description' => 'الحاق زمین به محدوده‌ی بافت روستا',
                'sort_order' => 206,
            ],

            // ══════════ 🔨 ساخت‌وساز تعمیری ══════════
            [
                'code' => 'B02', 'existing' => false,
                'name_fa' => 'عوارض بازسازی و تعمیر',
                'type' => 'levy', 'formula_key' => 'renovation',
                'category' => 'construction', 'subcategory' => 'renovation',
                'unit' => 'متر مربع', 'base_amount' => 350_000,
                'description' => 'تعمیر سازه، نما یا تأسیسات بنای موجود',
                'sort_order' => 301,
            ],
            [
                'code' => 'L07', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'renovation',
                'unit' => 'مورد', 'base_amount' => 850_000,
                'description' => 'تمدید پروانه‌ی ساختمانی منقضی‌شده',
                'sort_order' => 302,
            ],
            [
                'code' => 'B03', 'existing' => false,
                'name_fa' => 'عوارض تغییر کاربری',
                'type' => 'levy', 'formula_key' => 'land_use_change',
                'category' => 'construction', 'subcategory' => 'renovation',
                'unit' => 'متر مربع', 'base_amount' => 750_000,
                'description' => 'تغییر کاربری رسمی ملک (مثلاً مسکونی به تجاری)',
                'sort_order' => 303,
            ],
            [
                'code' => 'B04', 'existing' => false,
                'name_fa' => 'عوارض افزایش تراکم',
                'type' => 'levy', 'formula_key' => 'density_increase',
                'category' => 'construction', 'subcategory' => 'renovation',
                'unit' => 'متر مربع', 'base_amount' => 900_000,
                'description' => 'افزایش طبقات یا سطح اشغال بیش از پروانه',
                'sort_order' => 304,
            ],
            [
                'code' => 'S16', 'existing' => true,
                'category' => 'construction', 'subcategory' => 'renovation',
                'unit' => 'مورد', 'base_amount' => 450_000,
                'description' => 'صدور المثنی شناسنامه‌ی ساختمانی',
                'sort_order' => 305,
            ],
        ];

        $inserted = 0;
        $updated  = 0;

        foreach ($catalog as $row) {
            $code = $row['code'];
            $values = [
                'category'       => $row['category'],
                'subcategory'    => $row['subcategory'],
                'unit'           => $row['unit'],
                'description'    => $row['description'],
                'base_amount'    => $row['base_amount'],
                'sort_order'     => $row['sort_order'],
                'is_active'      => 1,
                'updated_at'     => now(),
            ];

            $exists = DB::table('tariff_titles')->where('code', $code)->exists();

            if ($exists) {
                DB::table('tariff_titles')->where('code', $code)->update($values);
                $updated++;
                continue;
            }

            // عنوان جدید — فیلدهای اجباری تکمیل می‌شوند
            DB::table('tariff_titles')->insert(array_merge($values, [
                'code'           => $code,
                'type'           => $row['type'],
                'name_fa'        => $row['name_fa'],
                'formula_key'    => $row['formula_key'] ?? null,
                'statutory_note' => null,
                'is_optional'    => 0,
                'effective_from' => $effective,
                'updated_by'     => null,
                'created_at'     => now(),
            ]));
            $inserted++;
        }

        // تاریخ اعمال پیش‌فرض برای آن‌هایی که خالی مانده‌اند
        DB::table('tariff_titles')
            ->whereNotNull('category')
            ->whereNull('effective_from')
            ->update(['effective_from' => $effective]);

        $this->command?->info("  ✅ کاتالوگ عوارض: {$updated} عنوان به‌روزرسانی شد، {$inserted} عنوان جدید اضافه شد.");
    }
}
