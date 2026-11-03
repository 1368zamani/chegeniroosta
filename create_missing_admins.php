<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Village;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== بررسی و ساخت ادمین برای روستاهای بخش مرکزی (section_id = 2) ===\n\n";

// گرفتن تمام روستاهای بخش مرکزی
$villages = Village::where('section_id', 2)->get();
$createdCount = 0;

foreach ($villages as $village) {
    // بررسی اینکه آیا برای این روستا ادمین وجود دارد یا خیر
    $existingAdmin = User::where('role', 'village_admin')
                         ->where('village_id', $village->id)
                         ->first();

    if (!$existingAdmin) {
        // ساخت نام کاربری بر اساس نام انگلیسی روستا (slug) یا ID
        $username = 'admin_' . $village->id;
        
        // اگر نام کاربری تکراری بود، یک پسوند به آن اضافه می‌کنیم
        if (User::where('username', $username)->exists()) {
            $username = 'admin_' . $village->id . '_' . time();
        }
        
        $defaultPassword = '12345678'; // رمز پیش‌فرض

        // ساخت کاربر بدون فعال کردن Observer های معیوب
        User::withoutEvents(function () use ($village, $username, $defaultPassword) {
            User::create([
                'name' => 'مدیر ' . $village->name,
                'username' => $username,
                'password' => Hash::make($defaultPassword),
                'role' => 'village_admin',
                'village_id' => $village->id,
                'section_id' => 2,
            ]);
        });

        echo "✅ ادمین ساخته شد برای: {$village->name} | نام کاربری: {$username} | رمز: {$defaultPassword}\n";
        $createdCount++;
    }
}

echo "\n═══════════════════════════════════════\n";
echo "تعداد ادمین‌های ساخته‌شده جدید: {$createdCount}\n";

$totalAdmins = User::where('role', 'village_admin')->where('section_id', 2)->count();
echo "تعداد کل ادمین‌های بخش مرکزی الان: {$totalAdmins}\n";
echo "═══════════════════════════════════════\n";
