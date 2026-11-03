<div dir="rtl">

# راهنمای استقرار

## دامنه‌ها و DNS

| نقش | نشانی |
| --- | --- |
| پورتال اصلی (شهرستان) | `chegeniroosta.ir` |
| پورتال بخش‌ها | `visian.chegeniroosta.ir`، `markazi.chegeniroosta.ir`، `shahivand.chegeniroosta.ir` |
| پورتال روستاها | `*.chegeniroosta.ir` (زیردامنهٔ هر روستا از فیلد `villages.subdomain`) |

رکوردهای DNS لازم:

```
A     @    SERVER_IP
A     *    SERVER_IP
```

زیردامنه‌ها باید در دیتابیس هم تعریف شده باشند (`sections.subdomain` و `villages.subdomain`)، وگرنه میان‌افزارها آن‌ها را نادیده می‌گیرند.

## پیش‌نیازهای سرور

- Ubuntu Server LTS
- Nginx + PHP 8.3 (با افزونه‌های `pdo`, `mbstring`, `xml`, `curl`, `zip`, `sqlite3`)
- Composer 2 و Node.js (برای build دارایی‌های Vite)
- SQLite یا MySQL/MariaDB — هر دو از طریق `DB_CONNECTION` پشتیبانی می‌شوند
- گواهی SSL: دامنهٔ اصلی و **وایلدکارد** (`*.chegeniroosta.ir`) برای پوشش همهٔ زیردامنه‌ها

## Nginx (نمونه)

```nginx
server {
    listen 443 ssl http2;
    server_name chegeniroosta.ir *.chegeniroosta.ir;

    root /var/www/chegeniroosta/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
}
```

## مراحل انتشار

```bash
git pull --ff-only
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

سرویس‌های پس‌زمینه:

- زمان‌بند: `* * * * * cd /var/www/chegeniroosta && php artisan schedule:run >> /dev/null 2>&1`
- صف: `php artisan queue:work --tries=3` زیر نظر systemd یا supervisor

## متغیرهای محیطی مهم

| کلید | مقدار در production |
| --- | --- |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://chegeniroosta.ir` |
| `APP_BASE_DOMAIN` | `chegeniroosta.ir` |
| `APP_KEY` | ثابت بماند؛ تغییرش باعث خروج کاربران و رمزگشایی‌نشدن داده‌ها می‌شود |
| `DB_CONNECTION` | `sqlite` یا `mysql` |
| `SESSION_DRIVER`، `CACHE_STORE`، `QUEUE_CONNECTION` | بر اساس زیرساخت (`database` پیش‌فرض) |

فایل `.env` هرگز در گیت قرار نمی‌گیرد.

## پشتیبان‌گیری

```bash
./scripts/backup.sh          # دیتابیس SQLite + storage/app/public در پوشهٔ backups/
php artisan app:backup       # همان کار از طریق دستور artisan با نگهداری N نسخهٔ آخر
```

پیش از هر انتشار یا مهاجرت، از دیتابیس و `storage/app/public` پشتیبان بگیرید.

## بررسی سلامت

- `GET /up` — بررسی سلامت داخلی لاراول
- `GET /api/v1/health` — پاسخ JSON با وضعیت و نسخه
- `./scripts/check-health.sh` و `./scripts/test-all.sh` — پایش دامنه‌ها و مسیرهای اصلی (مسیر `/var/www/chegeniroosta` در اسکریپت هاردکد است)
- `./scripts/site-audit.sh` — آدیت کامل و فقط‌خواندنی سرور

## چک‌لیست امنیتی

- [ ] `APP_DEBUG=false` و `APP_ENV=production`
- [ ] `.env` بیرون از گیت و با دسترسی محدود
- [ ] `storage` و `bootstrap/cache` قابل نوشتن برای کاربر وب‌سرور
- [ ] فایل‌های `vendor` و `node_modules` روی سرور بدون دسترسی عمومی
- [ ] گواهی SSL وایلدکارد فعال
- [ ] پشتیبان‌گیری زمان‌بندی‌شده و آزمون بازیابی
- [ ] رمزهای پیش‌فرض ادمین‌ها تغییر کرده باشد (`php artisan admins:set-village-password`)

</div>
