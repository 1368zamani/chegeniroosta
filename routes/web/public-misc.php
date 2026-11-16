<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VillageContactController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\VillageImageController;
use App\Http\Controllers\VillageNewsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Village Council Resolutions (Public)
|--------------------------------------------------------------------------
*/

Route::get('/village/{slug}/resolutions', [\App\Http\Controllers\PublicVillageSiteController::class, 'resolutions'])
    ->name('village.site.resolutions');

Route::get('/council', [\App\Http\Controllers\PublicSectionController::class, 'council'])
    ->middleware('section')
    ->name('section.site.council');

Route::get('/village/{slug}/dehyar', [\App\Http\Controllers\PublicVillageSiteController::class, 'dehyar'])
    ->name('village.site.dehyar');

// ═══════════════════════════════════════════════
// مسئولین شهرستان (Public)
// ═══════════════════════════════════════════════
Route::get('/officials/governor', [\App\Http\Controllers\PublicOfficialController::class, 'governor'])
    ->name('officials.governor');
Route::get('/officials/deputies', [\App\Http\Controllers\PublicOfficialController::class, 'deputies'])
    ->name('officials.deputies');
Route::get('/officials/deputy/{id}', [\App\Http\Controllers\PublicOfficialController::class, 'deputy'])
    ->name('officials.deputy');
Route::get('/officials/bakhshdars', [\App\Http\Controllers\PublicOfficialController::class, 'bakhshdars'])
    ->name('officials.bakhshdars');

/* ═══ محاسبه‌گر عمومی عوارض ═══ */

/* ═══ پرداخت دستی (فیش واریزی) ═══ */
/* ═══ پنل ادمین — مدیریت عوارض ═══ */
/* ═══ نمایش فایل فیش برای ادمین (محافظت‌شده) ═══ */
/* ═══ ورود اختصاصی کارشناس مالی و عمرانی بخشداری ═══ */

/* ═══ مدیریت کارشناسان مالی و عمرانی (پنل بخشداری) ═══ */


/* ═══ گزارش درآمد عوارض ═══ */
Route::get('/sitemap-news.xml', [\App\Http\Controllers\SitemapController::class, 'sitemapNews'])
    ->name('seo.sitemap.news');

/* ═══ مدیریت رمزهای کاربران (سوپرادمین + بخشدار) ═══ */
Route::middleware(["auth"])->prefix("admin")->group(function () {
    Route::get("/passwords", [\App\Http\Controllers\AdminPasswordController::class, "index"])
        ->name("admin.passwords.index");

    Route::post("/passwords/{user}/reset", [\App\Http\Controllers\AdminPasswordController::class, "reset"])
        ->name("admin.passwords.reset");
});

/* ═══ تغییر رمز عبور توسط خود کاربر (همه‌ی نقش‌ها) ═══ */
Route::middleware(['auth'])->prefix('admin/profile')->name('profile.')->group(function () {
    Route::get('/password', [\App\Http\Controllers\ProfileController::class, 'passwordForm'])
        ->name('password.edit');

    Route::post('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
        ->name('password.update');
});
/* ═══ مدیریت کارشناسان مالی و فنی/عمرانی ═══ */
/* ═══ ایجاد کاربر جدید ═══ */
Route::middleware('auth')->prefix('admin/village/{village}/dehyar')->name('admin.village.dehyar.')->group(function () {
    Route::post('/photo', [\App\Http\Controllers\Admin\DehyariPhotoController::class, 'upload'])->name('photo.upload');
    Route::delete('/photo', [\App\Http\Controllers\Admin\DehyariPhotoController::class, 'delete'])->name('photo.delete');
});

/* ═══ دانلود PDF قبض ═══ */
