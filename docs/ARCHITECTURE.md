<div dir="rtl">

# معماری و نقشهٔ کد

## چرخهٔ یک درخواست

1. `bootstrap/app.php` مسیرها (`web`، `api`، `console`، health در `/up`) و نام‌های مستعار میان‌افزار را ثبت می‌کند.
2. `IdentifySection` دامنه را می‌خواند: اگر میزبان زیردامنهٔ `config('app.base_domain')` باشد و آن زیردامنه در `sections.subdomain` وجود داشته باشد، شیء بخش در کانتینر با نام `currentSection` ثبت و با ویوها به اشتراک گذاشته می‌شود.
3. `IdentifyVillage` ادامه می‌دهد: اگر زیردامنه متعلق به یک بخش نباشد، آن را با `villages.subdomain` تطبیق می‌دهد.
4. `IdentifyAdminSubdomain` زیردامنهٔ `admin` را مدیریت می‌کند.
5. کنترل دسترسی با `CheckVillageAccess`، `EnsureExpertAccess` و `RequireTariffOfficerMode` اعمال می‌شود.

| نام مستعار (alias) | کلاس |
| --- | --- |
| `section` | `App\Http\Middleware\IdentifySection` |
| `village` | `App\Http\Middleware\IdentifyVillage` |
| `admin-section` | `App\Http\Middleware\IdentifyAdminSubdomain` |
| `village-access` | `App\Http\Middleware\CheckVillageAccess` |
| `expert.access` | `App\Http\Middleware\EnsureExpertAccess` |
| `fee-officer` | `App\Http\Middleware\FeeOfficerScope` — ⚠️ این کلاس وجود ندارد |

## چندمستأجری (tenancy)

- یک دیتابیس و یک کدبیس برای همهٔ بخش‌ها و روستاها.
- تفکیک داده با `section_id` و `village_id` روی مدل‌ها و کوئری‌ها.
- `sections.subdomain` و `villages.subdomain` یکتا هستند و مبنای تشخیص مستأجر در میان‌افزارها.
- دامنهٔ پایه: `config('app.base_domain')` ← متغیر محیطی `APP_BASE_DOMAIN` (پیش‌فرض `localhost`).
- همهٔ بخش‌ها روی یک اپلیکیشن واحد سرو می‌شوند؛ DNS وایلدکارد `*.chegeniroosta.ir` لازم است.

## نقش‌ها و دسترسی

`users.role`: `super_admin`، `section_admin`، `village_admin`، `fee_officer`

`users.officer_type` (برای کارشناسان عوارض): `financial` یا `technical`

مدل `User` این متدها را دارد: `isSuperAdmin()`، `isSectionAdmin()`، `isVillageAdmin()`، `canManageSection()`، `canManageVillage()`، `isFeeOfficer()`، `isSectionStaff()`، `isFinancialOfficer()`، `isTechnicalOfficer()`، `officerTypeLabel()`.

## پنل‌ها و کنترل‌رها

| پنل | مسیر | کنترل‌رها |
| --- | --- | --- |
| مدیریت مرکزی | `/admin` | `Admin/*` — کاربران، بخش‌ها، روستاها، مقامات، شورا، درخواست‌ها، آمار، تنظیمات، حساب‌های بانکی، تعرفه و کارشناسان |
| مدیریت روستا | `/admin/village/{village}` | `VillageAdmin/*` — خبر، رویداد، اماکن، جاذبه‌ها، شورا و مصوبات، دهیاری، محصولات، دانلودها، تصاویر، درخواست‌ها |
| کارشناس تعرفه | `/expert` | `Expert/*` — ورود، داشبورد، محتوای تعرفه، صورتحساب، حساب بانکی |
| کارشناس عوارض | `/tariff-officer` | `TariffOfficerAuthController` + میان‌افزار `RequireTariffOfficerMode` |
| عمومی | `/`، `/village/{slug}`، `/village/{slug}/tariff`، `/bakhshdar` | `Public/*`، `SectionSiteController`، `PublicVillageSiteController`، `TariffController`، `TariffCalculatorController`، `SitemapController`، `SearchController`، `DownloadController` |

## مدل‌های دامنه (۴۲ مدل)

- **بخش**: `Section`، `SectionNews`، `SectionNewsImage`، `SectionGallery`، `SectionCouncilMember`، `SectionRequest`، `SiteSetting`
- **روستا**: `Village`، `VillageNews`، `VillageNewsImage`، `VillageEvent`، `VillageFacility`، `VillageAttraction`، `VillageCouncilMember`، `VillageCouncilResolution`، `VillageProduct`، `VillageProductFile`، `VillageProductImage`، `VillageProject`، `VillageDownload`، `VillageImage`، `VillageContact`، `VillageRequest`
- **تعرفه و عوارض**: `TariffSet`، `TariffTitle`، `TariffItem`، `TariffParameter`، `TariffInstallmentRule`، `TariffExemption`، `TariffInvoice`، `TariffInvoiceLine`، `TariffReceipt`، `TariffChangeLog`، `TariffWorkflowLog`
- **کاربر و اداری**: `User`، `Dehyari`، `CountyOfficial`، `BankAccount`
- **آمار و ممیزی**: `ContentStat`، `ContentStatDaily`، `AuditLog`

## زیرسیستم تعرفه

`app/Services/Tariff/`:

- `TariffSetService` — ساخت و انتشار مجموعه‌های تعرفه
- `InstallmentCalculator` — محاسبهٔ اقساط
- `Calculators/` — محاسبه‌گرهای مستقل با `CalculatorRegistry` (املاک سالانه، متراژ با ضریب، کسب‌وکار، مادهٔ ۹۹، پروانه، فنس، درخت، ماشین‌آلات، خاکبرداری، پسماند، ارزش افزوده و …)

گردش کار صورتحساب شامل ثبت، تأیید کارشناس مالی/فنی، معافیت‌ها، رسید پرداخت، لاگ گردش کار و خروجی PDF (dompdf) و فرم‌های چاپی است. معافیت‌ها و تغییرات با `TariffExemption` و `TariffWorkflowLog` ردیابی می‌شوند و دسترسی با `TariffSetPolicy` کنترل می‌شود.

## API نسخهٔ ۱

مسیرها در `routes/api.php` با پیشوند `/api/v1`:

| متد و مسیر | کار |
| --- | --- |
| `GET /health` | بررسی سلامت سرویس |
| `GET /sections`، `/sections/{slug}` | فهرست و جزئیات بخش |
| `GET /sections/{slug}/villages`، `/sections/{slug}/news` | روستاها و اخبار بخش |
| `GET /villages`، `/villages/{slug}` | فهرست و جزئیات روستا |
| `GET /villages/{slug}/gallery`، `/villages/{slug}/news` | گالری و اخبار روستا |
| `GET /news`، `/news/{slug}` | اخبار |
| `GET /search` | جستجو |
| `GET /stats` | آمار کلی |

## بخش‌های کمکی

- `app/Helpers/jalali.php` و `app/Helpers/settings.php` (تابع `setting()`؛ در `composer.json` به‌صورت autoload files ثبت شده‌اند)
- `app/Support/Number.php` (تبدیل ارقام) و `app/Support/PasswordPolicy.php`
- `app/Observers/VillageObserver.php` و `app/Policies/TariffSetPolicy.php`
- دستورات artisan: `app:backup`، `import:section`، `import:visian`، `section:bakhshdar`، `admins:set-village-password`
- Seederها: `AdminUserSeeder`، `VillageSeeder`، `TariffCatalogSeeder`، `VaysianTariff1405Seeder`، `WorksFeeCatalogSeeder`، `WorksFeeItemsSeeder`

## تست‌ها

در `tests/Feature` (شامل `AdminAccessTest`، `TariffWorkflowTest` و `tests/Feature/Tariff`) و `tests/Unit`. اجرا با `composer test` یا `php artisan test`.

</div>
