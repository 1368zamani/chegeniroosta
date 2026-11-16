<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== بررسی و ساخت ادمین‌های گم‌شده برای بخش مرکزی ===\n\n";

// گرفتن تمام روستاهای بخش مرکزی (section_id = 2)
$villages = DB::table('villages')->where('section_id', 2)->get();
$createdCount = 0;

foreach ($villages as $village) {
    // آیا این روستا ادمین دارد؟
    $hasAdmin = DB::table('users')
        ->where('role', 'village_admin')
        ->where('village_id', $village->id)
        ->exists();

    if (!$hasAdmin) {
        $username = 'admin_' . $village->id;
        $email = 'admin_' . $village->id . '@chegeniroosta.ir';
        $password = Hash::make('12345678');

        // درج مستقیم در دیتابیس برای دور زدن محدودیت‌های مدل
        DB::table('users')->insert([
            'name'        => 'مدیر ' . $village->name,
            'username'    => $username,
            'email'       => $email,
            'password'    => $password,
            'role'        => 'village_admin',
            'section_id'  => 2,
            'village_id'  => $village->id,
            'is_active'   => 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        echo "✅ ادمین ساخته شد: {$village->name} (ID: {$village->id}) | یوزرنیم: {$username} | رمز: 12345678\n";
        $createdCount++;
    }
}

echo "\n═══════════════════════════════════════\n";
echo "تعداد ادمین‌های جدید ساخته‌شده: {$createdCount}\n";

$total = DB::table('users')
    ->where('role', 'village_admin')
    ->where('section_id', 2)
    ->count();
echo "تعداد کل ادمین‌های بخش مرکزی الان: {$total}\n";
echo "═══════════════════════════════════════\n";
