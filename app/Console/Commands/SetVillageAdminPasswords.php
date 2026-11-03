<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * تنظیم رمز عبور همه‌ی ادمین‌های روستا.
 *
 * استفاده:
 *   php artisan admins:set-village-password             → رمز پیش‌فرض 888631
 *   php artisan admins:set-village-password 12345abc    → رمز دلخواه
 *   php artisan admins:set-village-password --force      → بدون پرسش تأیید
 */
class SetVillageAdminPasswords extends Command
{
    protected $signature = 'admins:set-village-password
                            {password=888631 : رمز جدید}
                            {--force : بدون پرسش تأیید}';

    protected $description = 'تنظیم رمز عبور همه‌ی ادمین‌های روستا';

    public function handle(): int
    {
        $password = (string) $this->argument('password');

        if ($password === '') {
            $this->error('رمز نمی‌تواند خالی باشد.');
            return self::FAILURE;
        }

        $users = User::where('role', 'village_admin')->get();

        if ($users->isEmpty()) {
            $this->warn('⚠️ هیچ ادمین روستایی پیدا نشد.');
            return self::SUCCESS;
        }

        $this->line('  تعداد ادمین‌های روستا: ' . $users->count());

        if (! $this->option('force')
            && ! $this->confirm("رمز همه‌ی این ادمین‌ها به «{$password}» تغییر کند؟")) {
            $this->warn('لغو شد.');
            return self::SUCCESS;
        }

        $count = 0;

        foreach ($users as $user) {
            // هش جداگانه برای هر کاربر (نمک متفاوت)
            $user->password = Hash::make($password);
            $user->save();
            $count++;
        }

        $this->info("✅ رمز {$count} ادمین روستا تنظیم شد.");

        return self::SUCCESS;
    }
}
