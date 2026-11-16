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
| Village Site (سامانه جامع روستاها)
|--------------------------------------------------------------------------
*/

Route::prefix('village/{slug}')->group(function () {

    Route::get('/', [\App\Http\Controllers\PublicVillageSiteController::class, 'home'])
        ->name('village.site.home');

    Route::get('/news', [\App\Http\Controllers\PublicVillageSiteController::class, 'newsList'])
        ->name('village.site.news');

    Route::get('/news/{newsSlug}', [\App\Http\Controllers\PublicVillageSiteController::class, 'newsShow'])
        ->name('village.site.news.show');

    Route::get('/projects', [\App\Http\Controllers\PublicVillageSiteController::class, 'projects'])
        ->name('village.site.projects');

    Route::get('/about', [\App\Http\Controllers\PublicVillageSiteController::class, 'about'])
        ->name('village.site.about');
        Route::get('/attractions', [\App\Http\Controllers\PublicVillageSiteController::class, 'attractions'])->name('village.site.attractions');
        Route::get('/gallery', [\App\Http\Controllers\PublicVillageSiteController::class, 'gallery'])->name('village.site.gallery');

    Route::get('/council', [\App\Http\Controllers\PublicVillageSiteController::class, 'council'])
        ->name('village.site.council');

    Route::get('/facilities', [\App\Http\Controllers\PublicVillageSiteController::class, 'facilities'])
        ->name('village.site.facilities');

    Route::get('/events', [\App\Http\Controllers\PublicVillageSiteController::class, 'events'])
        ->name('village.site.events');

    Route::get('/request', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestCreate'])
        ->name('village.site.request.create');

    Route::post('/request', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestStore'])
        ->name('village.site.request.store');

    Route::get('/request/success/{code}', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestSuccess'])
        ->name('village.site.request.success');

    Route::get('/track', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestTrack'])
        ->name('village.site.request.track');

    Route::post('/track', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestTrackResult'])
        ->name('village.site.request.track.result');

    // دانلود فایل‌های روستا (با شمارش تعداد دانلود)
    Route::get('/download/{id}', [\App\Http\Controllers\PublicVillageSiteController::class, 'downloadFile'])
        ->where('id', '[0-9]+')
        ->name('village.site.download');

    // مرکز دانلود روستا (صفحه‌ی فهرست فایل‌ها)
    Route::get('/downloads', [\App\Http\Controllers\PublicVillageSiteController::class, 'downloads'])
        ->name('village.site.downloads');

    // ─── محصولات روستا (فروش) ───
    Route::get('/products', [\App\Http\Controllers\PublicVillageSiteController::class, 'products'])
        ->name('village.site.products');

    Route::get('/products/{productSlug}', [\App\Http\Controllers\PublicVillageSiteController::class, 'productShow'])
        ->name('village.site.products.show');

    // دانلود فایل ضمیمه‌ی محصول (با شمارش)
    Route::get('/product-file/{id}', [\App\Http\Controllers\PublicVillageSiteController::class, 'productFile'])
        ->where('id', '[0-9]+')
        ->name('village.site.product.file');
});

/*
|--------------------------------------------------------------------------
| Public Village Profile
|--------------------------------------------------------------------------
*/

Route::get('/village/{slug}', [\App\Http\Controllers\PublicVillageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('public.village.show');
