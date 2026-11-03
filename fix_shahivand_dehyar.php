<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Village;

echo "=== اصلاح اطلاعات دهیار برای روستاهای بخش شاهیوند (section_id = 3) ===\n\n";

// گرفتن تمام روستاهای شاهیوند که نام دهیار دارند
$villages = Village::where('section_id', 3)->whereNotNull('dehyar_name')->get();
$updatedCount = 0;

foreach ($villages as $village) {
    // پر کردن فیلدهای خالی با متن پیش‌فرض
    $changed = false;

    if (empty($village->dehyar_bio)) {
        $village->dehyar_bio = "دهیار محترم و فعال روستای " . $village->name;
        $changed = true;
    }
    if (empty($village->dehyar_education)) {
        $village->dehyar_education = "در حال تکمیل اطلاعات تحصیلی";
        $changed = true;
    }
    if (empty($village->dehyar_experience)) {
        $village->dehyar_experience = "در حال تکمیل سوابق شغلی";
        $changed = true;
    }
    if (empty($village->dehyar_hours)) {
        $village->dehyar_hours = "شنبه تا چهارشنبه ۸ تا ۱۴";
        $changed = true;
    }

    if ($changed) {
        $village->saveQuietly();
        echo "✅ اطلاعات دهیار برای '{$village->name}' تکمیل شد.\n";
        $updatedCount++;
    }
}

echo "\n═══════════════════════════════════════\n";
echo "تعداد روستاهایی که اطلاعاتشان تکمیل شد: {$updatedCount}\n";
echo "═══════════════════════════════════════\n";
