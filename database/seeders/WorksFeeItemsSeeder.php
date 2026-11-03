<?php

namespace Database\Seeders;

use App\Models\TariffChangeLog;
use App\Models\TariffSet;
use App\Models\TariffTitle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * افزودن «اقلام دسته‌بندی‌شده‌ی جدید» به تعرفه‌ی همه‌ی روستاها.
 *
 * چرا لازم است؟ تعرفه‌های ۱۳۰ روستا قبل از بازطراحی «عوارض» ساخته شده
 * بودند، پس ۶ عنوان جدید (نخاله، پسماند کشاورزی، صدور پروانه، بازسازی،
 * تغییر کاربری، افزایش تراکم) هیچ ردیفی در جدول tariff_items نداشتند و
 * در صفحه‌ی شهروند «غیرقابل انتخاب» نمایش داده می‌شدند.
 *
 * ⚠️ مبالغ با «مبلغ پایه»ی همان عنوان (که در پنل کارشناس قابل ویرایش است)
 * وارد می‌شوند. مبلغ نهایی باید توسط کارشناس عمران و بر اساس مصوبه‌ی
 * شورای اسلامی هر روستا اصلاح شود.
 *
 * اجرا:  php artisan db:seed --class=WorksFeeItemsSeeder
 *
 * این Seeder idempotent است — اگر ردیفی از قبل وجود داشته باشد
 * دست به آن نمی‌زند، پس چند بار اجرا شدنش بی‌خطر است.
 */
class WorksFeeItemsSeeder extends Seeder
{
    public function run(): void
    {
        // فقط عناوینی که دسته‌بندی شده و فعال‌اند
        $titles = TariffTitle::categorized()->active()->ordered()->get();

        if ($titles->isEmpty()) {
            $this->command?->warn('  ⚠️ هیچ عنوان دسته‌بندی‌شده‌ای پیدا نشد. اول WorksFeeCatalogSeeder را اجرا کن.');

            return;
        }

        // همه‌ی مجموعه‌های تعرفه (هر روستا در هر سال)
        $sets = TariffSet::query()
            ->select('id', 'village_id', 'jalali_year', 'status')
            ->orderBy('id')
            ->get();

        if ($sets->isEmpty()) {
            $this->command?->warn('  ⚠️ هیچ مجموعه‌ی تعرفه‌ای وجود ندارد.');

            return;
        }

        // ردیف‌های موجود: کلید = "setId|titleId"
        $existing = DB::table('tariff_items')
            ->select('tariff_set_id', 'tariff_title_id')
            ->get()
            ->keyBy(fn ($r) => $r->tariff_set_id . '|' . $r->tariff_title_id);

        $now = now();
        $rows = [];
        $affectedTitles = [];

        foreach ($sets as $set) {
            foreach ($titles as $title) {
                $key = $set->id . '|' . $title->id;

                if ($existing->has($key)) {
                    continue; // از قبل وجود دارد — دست نزن
                }

                $amount = (int) ($title->base_amount ?? 0);

                $rows[] = [
                    'tariff_set_id'   => $set->id,
                    'tariff_title_id' => $title->id,
                    'is_active'       => 1,
                    'previous_amount' => 0,
                    'increase_percent'=> 0,
                    'params'          => null,
                    'final_amount'    => $amount,
                    'unit'            => $title->unit ?: 'مورد',
                    'notes'           => 'مبلغ پایه — نیازمند تأیید کارشناس عمران',
                    'formula_display' => null,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ];

                $affectedTitles[$title->id] = $title;
            }
        }

        if (empty($rows)) {
            $this->command?->info('  ℹ️ همه‌ی روستاها از قبل این اقلام را دارند — تغییری لازم نبود.');

            return;
        }

        // درج دسته‌ای (هر ۵۰۰ ردیف یک بار، تا از سقف بسته‌ی MySQL رد نشویم)
        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('tariff_items')->insert($chunk);
            }
        });

        // ثبت در تاریخچه برای هر عنوان (ردیابی)
        if (auth()->check() === false) {
            // در حالت CLI کاربری وجود ندارد؛ لاگ با نام «سیستم» ثبت می‌شود
        }

        foreach ($affectedTitles as $title) {
            try {
                TariffChangeLog::create([
                    'tariff_title_id' => $title->id,
                    'user_id'         => auth()->id(),
                    'user_name'       => auth()->user()?->name ?: 'سیستم (Seeder)',
                    'action'          => TariffChangeLog::ACTION_UPDATE_INFO,
                    'old_amount'      => null,
                    'new_amount'      => (int) $title->base_amount,
                    'old_values'      => null,
                    'new_values'      => ['propagated_to_sets' => count($sets)],
                    'reason'          => 'افزودن این قلم به تعرفه‌ی ' . count($sets) . ' روستا (مبلغ پایه — نیاز به تأیید)',
                    'ip_address'      => null,
                    'created_at'      => now(),
                ]);
            } catch (\Throwable $e) {
                // ثبت لاگ نباید کل عملیات را برگرداند
            }
        }

        $this->command?->info(sprintf(
            '  ✅ %s ردیف برای %s عنوان در %s مجموعه تعرفه اضافه شد.',
            number_format(count($rows)),
            number_format(count($affectedTitles)),
            number_format(count($sets))
        ));
    }
}
