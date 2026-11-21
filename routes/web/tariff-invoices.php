<?php

use App\Http\Controllers\LegacyLevyController;
use App\Http\Controllers\Public\PublicLevyController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════
// فاز ۱۰: RSS و Sitemap اختصاصی روستا
// ═══════════════════════════════════════════════════════════
Route::get('/village/{slug}/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'villageSitemap'])->name('village.sitemap');
Route::get('/village/{slug}/feed.xml',    [\App\Http\Controllers\SitemapController::class, 'villageRss'])->name('village.rss');
Route::get('/villages/sitemap.xml',       [\App\Http\Controllers\SitemapController::class, 'villagesIndex'])->name('villages.sitemap.index');

// ═══════════════════════════════════════════════════════════
// محاسبه / ثبت توسط کارشناس (مسیر قدیمی → کنترلر جدید)
// ═══════════════════════════════════════════════════════════
Route::middleware(['web', 'auth'])->prefix('village/{slug}/tariff')->group(function () {
    Route::post('/calculate',  [PublicLevyController::class, 'calculate'])->name('tariff.edit.calculate');
    Route::post('/save',       [PublicLevyController::class, 'store'])->name('tariff.edit.update');
    Route::get('/history/{item}', [LegacyLevyController::class, 'index'])->name('tariff.edit.history');
});

// ═══════════════════════════════════════════════════════════
// صفحهٔ عمومی عوارض روستا — جایگزین صفحهٔ بایگانی‌شده
// ═══════════════════════════════════════════════════════════
// نکته: هر نام مسیر باید URI منحصربهفردی داشتهباشد؛ لاراول نامهای تکراری
// روی یک URI را هنگام ساخت جدول مسیرها حذف میکند. aliasها URI جداگانه دارند.
Route::prefix('village/{slug}/tariff')->group(function () {
    Route::get('/',                 [PublicLevyController::class, 'index'])->name('village.tariff.index');
    Route::post('/invoice',         [PublicLevyController::class, 'store'])->name('village.tariff.invoice.issue');
    Route::post('/track',           [PublicLevyController::class, 'track'])->name('village.tariff.track');
    Route::get('/track',            [PublicLevyController::class, 'trackForm'])->name('tariff.invoice.track.form');

    // نامهای قدیمی که هنوز در برخی لینکها استفاده میشوند (URI مستقل)
    Route::get('/public',           [PublicLevyController::class, 'index'])->name('tariff.public');
    Route::get('/create',           [PublicLevyController::class, 'index'])->name('tariff.invoice.create');
    Route::post('/create',          [PublicLevyController::class, 'store'])->name('tariff.invoice.store');
    Route::post('/invoice/track',   [PublicLevyController::class, 'track'])->name('tariff.invoice.track');
});

Route::get('/tariff/invoice/{token}', [PublicLevyController::class, 'show'])->name('village.tariff.invoice.show');
Route::post('/tariff/invoice/{token}/receipt', [PublicLevyController::class, 'receipt'])->name('village.tariff.invoice.receipt');

// نامهای قدیمی فیش / رسید (URI مستقل)
Route::get('/tariff/levy/request/{token}', [PublicLevyController::class, 'show'])->name('tariff.invoice.show');
Route::post('/tariff/levy/request/{token}/receipt', [PublicLevyController::class, 'receipt'])->name('tariff.invoice.receipt');

// ═══════════════════════════════════════════════════════════
// پنل ادمین فیشهای قدیم — بایگانی شده (بدون تغییر رفتار)
// ═══════════════════════════════════════════════════════════
Route::middleware(['web', 'auth'])->prefix('admin/tariff-invoices')->name('admin.tariff-invoices.')->group(function () {
    Route::get('/', [LegacyLevyController::class, 'index'])->name('index');
    Route::get('/account', [LegacyLevyController::class, 'index'])->name('account');
    Route::post('/account', [LegacyLevyController::class, 'index'])->name('account.save');
    Route::get('/{id}', [LegacyLevyController::class, 'index'])->where('id', '[0-9]+')->name('show');
    Route::post('/receipts/{receiptId}/approve', [LegacyLevyController::class, 'index'])->where('receiptId', '[0-9]+')->name('receipts.approve');
    Route::post('/receipts/{receiptId}/reject', [LegacyLevyController::class, 'index'])->where('receiptId', '[0-9]+')->name('receipts.reject');
    Route::post('/{id}/cancel', [LegacyLevyController::class, 'index'])->where('id', '[0-9]+')->name('cancel');
    Route::post('/{id}/restore', [LegacyLevyController::class, 'index'])->where('id', '[0-9]+')->name('restore');
    Route::delete('/{id}', [LegacyLevyController::class, 'index'])->where('id', '[0-9]+')->name('destroy');
    Route::post('/{id}/status', [LegacyLevyController::class, 'index'])->where('id', '[0-9]+')->name('status');
});

Route::middleware(['web', 'auth'])->prefix('admin/village/{village}/tariff-reports')->name('admin.village.tariff-reports.')->group(function () {
    Route::get('/', [LegacyLevyController::class, 'index'])->name('index');
    Route::get('/summary', [LegacyLevyController::class, 'index'])->name('summary');
    Route::get('/{id}', [LegacyLevyController::class, 'index'])->where('id', '[0-9]+')->name('show');
});
