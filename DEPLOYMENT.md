# استقرار نهایی روستا نما

## دامنه اصلی

chegeniroosta.ir

## دامنه مدیریت

admin.chegeniroosta.ir

## زیردامنه روستاها

sabzevar.chegeniroosta.ir
cham-divan.chegeniroosta.ir
abuzar.chegeniroosta.ir

## Wildcard DNS

*.chegeniroosta.ir

## سرور پیشنهادی

Ubuntu Server LTS
Nginx
PHP
MySQL/MariaDB
Composer
Node.js
SSL Let's Encrypt

## ساختار

یک Laravel Application

یک Database

هر روستا با village_id و subdomain از سایر روستاها تفکیک می‌شود.

## DNS

A     @       SERVER_IP
A     admin   SERVER_IP
A     *       SERVER_IP

## وب‌سرور

Nginx باید درخواست‌های زیر را به یک Laravel Application ارسال کند:

chegeniroosta.ir
admin.chegeniroosta.ir
*.chegeniroosta.ir

## SSL

برای دامنه اصلی، admin و wildcard گواهی SSL تنظیم شود.

## نکته مهم

قبل از انتقال اطلاعات:

1. از database.sqlite پشتیبان گرفته شود.
2. از storage/app/public پشتیبان گرفته شود.
3. APP_DEBUG در محیط production برابر false باشد.
4. APP_ENV در محیط production برابر production باشد.
5. APP_KEY حفظ شود و تغییر نکند.
6. فایل .env در Git قرار نگیرد.


---

## رویهٔ استقرار (Deploy)

به‌روزرسانی: ۱۴۰۵/۰۸/۲۵

### دستور سریع

```bash
sudo bash /var/www/chegeniroosta/scripts/deploy.sh <branch>
```

اسکریپت به‌ترتیب: بکاپ می‌گیرد، `git pull --ff-only` می‌زند، `composer install`، مایگریشن، بازسازی کش‌ها، ری‌لود PHP-FPM و ری‌استارت صف را انجام می‌دهد و در پایان هر ۴ دامنه را چک می‌کند.

### نکات مهم

1. **OPcache**: `opcache.validate_timestamps=0` شده است. یعنی هر تغییری در فایل‌های PHP **تا زمانی که `systemctl reload php8.5-fpm` اجرا نشود اعمال نمی‌شود**. هرگز فایل را دستی روی سرور ویرایش نکنید؛ از گیت دیپلوی کنید.
2. **Cache و Session روی Redis** هستند (دیتابیس‌های ۳ و ۴). دیتابیس ۰ و ۱ برای بقیهٔ پروژه‌ها آزاد است. `QUEUE_CONNECTION` عمداً روی `database` مانده است.
3. **کد زنده باید همیشه با گیت یکی باشد.** اگر روی سرور تغییری دادید، همان روز کامیت و پوش کنید.
4. **بکاپ روزانه** ساعت ۳:۳۰ در `/root/backups-comprehensive` و ساعت ۴:۰۰ به گوگل‌درایو منتقل می‌شود (`/var/log/comprehensive-backup.log` و `/var/log/offsite-backup.log`).
5. **تست‌ها** با SQLite حافظه‌ای اجرا می‌شوند و به دیتابیس واقعی دست نمی‌زنند: `sudo -u www-data php artisan test`.
6. فایل `.env` در گیت نیست؛ تغییرات آن را مستند کنید (`.env.example` مرجع است).
