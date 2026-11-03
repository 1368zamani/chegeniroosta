<div dir="rtl">

# چگنی‌روستا — سامانهٔ اطلاع‌رسانی روستایی شهرستان چگنی

سامانهٔ رسمی معرفی و اطلاع‌رسانی بخش‌ها و روستاهای شهرستان چگنی (استان لرستان).

یک اپلیکیشن Laravel که به‌صورت **چندبخشی (multi-tenant)** روی یک کدبیس و یک دیتابیس اجرا می‌شود: هر بخش و هر روستا نشانی اختصاصی خود را دارد و محتوایش با `section_id` و `village_id` تفکیک می‌شود.

| موضوع | مقدار |
| --- | --- |
| دامنهٔ اصلی | `https://chegeniroosta.ir` |
| بخش‌ها | `visian` · `markazi` · `shahivand` (هرکدام روی زیردامنهٔ خودش) |
| روستاها | زیردامنهٔ اختصاصی هر روستا روی `*.chegeniroosta.ir` |
| پنل مدیریت مرکزی | `/admin` |
| پنل کارشناسان تعرفه | `/expert` و `/tariff-officer` |
| API | `/api/v1` |

## هستهٔ فناوری

- PHP `^8.3` و Laravel `^13.17`
- Blade + Vite + Tailwind CSS
- دیتابیس: SQLite (پیش‌فرض `.env.example`) یا MySQL/MariaDB
- `morilog/jalali` برای تاریخ شمسی و `barryvdh/laravel-dompdf` برای PDF صورتحساب
- تست: PHPUnit 12 · فرمت کد: Laravel Pint

## قابلیت‌ها

- **پورتال هر بخش**: صفحهٔ اصلی، اخبار و مطالب بخش، گالری، شورای بخش، معرفی بخشدار، درخواست‌های مردمی و آمار بازدید
- **پورتال هر روستا**: خبر، رویداد، اماکن و جاذبه‌ها، شورا و مصوبات، دهیاری، محصولات، دانلودها، گالری تصاویر و اطلاعات تماس
- **سامانهٔ تعرفه و عوارض**: مجموعه‌های تعرفه، اقلام و پارامترها، محاسبه‌گرها (املاک، کسب‌وکار، نوسازی، مادهٔ ۹۹، …)، صورتحساب و اقساط، معافیت‌ها، رسید پرداخت، گردش کار تأیید (کارشناس مالی و فنی)، خروجی PDF و فرم‌های چاپی
- **پنل‌های مدیریت**: مدیریت مرکزی، بخش‌ها، روستاها و کارشناسان
- **SEO**: `sitemap.xml`، `sitemap-news.xml`، sitemap روستاها و `feed.xml`
- **API نسخهٔ ۱**: بخش‌ها، روستاها، اخبار، جستجو و آمار
- **آمار بازدید محتوا** به‌صورت روزانه (`content_stats` / `content_stat_daily`)

## راه‌اندازی محلی

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

شناسایی بخش و روستا روی زیردامنه انجام می‌شود و دامنهٔ پایه از `APP_BASE_DOMAIN` خوانده می‌شود؛ برای محیط محلی:

```env
APP_BASE_DOMAIN=localhost
```

سپس پورتال بخش‌ها با نشانی‌هایی مثل `http://visian.localhost:8000` باز می‌شود.

## تست و کیفیت کد

```bash
composer test                 # پاک‌سازی کش کانفیگ و اجرای تست‌ها
php artisan test
./vendor/bin/pint --test
```

## ساختار پروژه

```
app/
  Console/Commands/   دستورات نگهداری (پشتیبان‌گیری، ورود داده‌ها، رمز ادمین‌ها)
  Helpers/            jalali.php (تاریخ شمسی) و settings.php (تابع setting())
  Http/Controllers/   کنترل‌رها، تفکیک‌شده بر اساس پنل (Admin, VillageAdmin, Expert, Public, Api)
  Http/Middleware/    تشخیص بخش/روستا و کنترل دسترسی
  Models/             مدل‌های دامنه (بخش، روستا، محتوا، تعرفه، کاربر، آمار)
  Services/Tariff/    منطق تعرفه، اقساط و محاسبه‌گرها
  Support/            ابزارهای کمکی (تبدیل اعداد، سیاست رمز عبور)
docs/                 معماری، استقرار و راهنمای اسکریپت‌ها
scripts/              اسکریپت‌های عملیاتی (پشتیبان‌گیری، پایش، آدیت)
```

جزئیات کامل در [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md).

## مستندات

- [معماری و نقشهٔ کد](docs/ARCHITECTURE.md)
- [راهنمای استقرار](docs/DEPLOYMENT.md)
- [اسکریپت‌های عملیاتی](docs/SCRIPTS.md)

## بدهی فنی شناخته‌شده

- میان‌افزار `fee-officer` در `bootstrap/app.php` به کلاس ناموجود `App\Http\Middleware\FeeOfficerScope` اشاره می‌کند؛ کلاس موجود `RequireTariffOfficerMode` است. تا وقتی هیچ مسیری از این نام مستعار استفاده نکند خطایی رخ نمی‌دهد.
- یک مایگریشن با پسوند `.disabled` در `database/migrations` نگه داشته شده است.
- `routes/web.php` حدود ۱۱۰۰ خط است و نامزد تفکیک به فایل‌های کوچک‌تر بر اساس پنل است.
- `.env.example` هنوز مقادیر پیش‌فرض اسکلت لاراول را دارد (`APP_NAME=Laravel`، `APP_LOCALE=en`).
- چند اسکریپت PHP یک‌بارمصرف در ریشهٔ پروژه باقی مانده‌اند؛ بهتر است به دستورات artisan تبدیل شوند.

</div>
