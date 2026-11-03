<div dir="rtl">

# اسکریپت‌های عملیاتی

همهٔ اسکریپت‌های پوسته در پوشهٔ `scripts/` هستند.

| فایل | کار | نکته |
| --- | --- | --- |
| `scripts/backup.sh` | پشتیبان‌گیری از دیتابیس SQLite و `storage/app/public` در پوشهٔ `backups/` | مسیر پروژه را از محل خود اسکریپت (پوشهٔ والد) تشخیص می‌دهد |
| `scripts/check-health.sh` | پایش سلامت دامنه‌ها، مسیرهای اصلی، وضعیت گیت و سرویس‌ها با `curl` | مسیر `/var/www/chegeniroosta` هاردکد است |
| `scripts/check-site.sh` | چک سریع صفحهٔ اصلی و پورتال بخش ویسیان با کوکی | مسیر هاردکد |
| `scripts/test-all.sh` | تست جامع نقطه‌های پایانی سایت | مسیر هاردکد |
| `scripts/site-audit.sh` | آدیت کامل و **فقط‌خواندنی** سرور؛ گزارش در `/root/site-audit-<date>/` | نیازمند دسترسی ریشه |
| `scripts/prepare-production.sh` | بررسی پیش از انتشار (نسخهٔ لاراول، تنظیمات محیطی) | از ریشهٔ پروژه اجرا شود |

اجرا:

```bash
chmod +x scripts/*.sh   # در صورت نیاز
./scripts/check-health.sh
```

## اسکریپت‌های یک‌بارمصرف (باقی‌مانده در ریشه)

این فایل‌ها داده‌های production را یک‌بار اصلاح کرده‌اند و به‌دلیل وابستگی به مسیر اجرا (`require __DIR__.'/vendor/autoload.php'`) فعلاً در ریشهٔ پروژه مانده‌اند:

- `create_missing_admins.php` — ساخت ادمین برای روستاهای بخش مرکزی
- `fix_admins_v2.php` — اصلاح ادمین‌های گم‌شدهٔ بخش مرکزی
- `fix_shahivand_dehyar.php` — اصلاح اطلاعات دهیار روستاهای شاهیوند
- `update_shahivand.php` — به‌روزرسانی داده‌های شاهیوند

اجرای صحیح از ریشهٔ پروژه:

```bash
php create_missing_admins.php
```

**پیشنهاد:** این چهار فایل به دستورات artisan تبدیل شوند (مانند `admins:set-village-password`) و از ریشه حذف گردند تا کد عملیاتی تنها در `app/Console/Commands` نگهداری شود.

</div>
