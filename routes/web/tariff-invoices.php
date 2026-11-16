<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VillageContactController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\VillageImageController;
use App\Http\Controllers\VillageNewsController;
use Illuminate\Support\Facades\Route;


// ═══════════════════════════════════════════════════════════
// فاز ۱۰: RSS و Sitemap اختصاصی روستا
// ═══════════════════════════════════════════════════════════
Route::get('/village/{slug}/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'villageSitemap'])->name('village.sitemap');
Route::get('/village/{slug}/feed.xml',    [\App\Http\Controllers\SitemapController::class, 'villageRss'])->name('village.rss');
Route::get('/villages/sitemap.xml',       [\App\Http\Controllers\SitemapController::class, 'villagesIndex'])->name('villages.sitemap.index');

// ═══════════════════════════════════════════════════════════
// سیستم ویرایش تعرفه توسط کارشناسان
// ═══════════════════════════════════════════════════════════
Route::middleware(['web', 'auth'])->prefix('village/{slug}/tariff')->group(function () {
    Route::post('/calculate',  [\App\Http\Controllers\Public\TariffEditController::class, 'calculate'])
        ->name('tariff.edit.calculate');
    Route::post('/save',       [\App\Http\Controllers\Public\TariffEditController::class, 'update'])
        ->name('tariff.edit.update');
    Route::get('/history/{item}', [\App\Http\Controllers\Public\TariffEditController::class, 'history'])
        ->name('tariff.edit.history');
});

// ═══════════════════════════════════════════════════════════
// فاز فیش‌دهی عوارض
// ═══════════════════════════════════════════════════════════

// عمومی — برای هر روستا
Route::prefix('village/{slug}/tariff/invoice')->name('tariff.invoice.')->group(function () {
    Route::get('/create', [\App\Http\Controllers\Public\TariffInvoiceController::class, 'create'])
        ->name('create');
    Route::post('/', [\App\Http\Controllers\Public\TariffInvoiceController::class, 'store'])
        ->name('store');
    Route::get('/track', [\App\Http\Controllers\Public\TariffInvoiceController::class, 'trackForm'])
        ->name('track.form');
    Route::post('/track', [\App\Http\Controllers\Public\TariffInvoiceController::class, 'track'])
        ->name('track');
});

// نمایش فیش با توکن (بدون نیاز به slug)
Route::get('/tariff/invoice/{token}', [\App\Http\Controllers\Public\TariffInvoiceController::class, 'show'])
    ->name('tariff.invoice.show');

Route::post('/tariff/invoice/{token}/receipt', [\App\Http\Controllers\Public\TariffInvoiceController::class, 'uploadReceipt'])
    ->name('tariff.invoice.receipt');

// ═══════════════════════════════════════════════════════════
// پنل ادمین — فیش‌های عوارض
// ═══════════════════════════════════════════════════════════
Route::middleware(['web', 'auth'])->prefix('admin/tariff-invoices')->name('admin.tariff-invoices.')->group(function () {

    // لیست فیش‌ها
    Route::get('/', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'index'])
        ->name('index');

    // حساب بانکی
    Route::get('/account', [\App\Http\Controllers\Admin\AdminBankAccountController::class, 'edit'])
        ->name('account');
    Route::post('/account', [\App\Http\Controllers\Admin\AdminBankAccountController::class, 'save'])
        ->name('account.save');

    // نمایش یک فیش
    Route::get('/{id}', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('show');

    // تایید / رد رسید
    Route::post('/receipts/{receiptId}/approve', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'approveReceipt'])
        ->where('receiptId', '[0-9]+')
        ->name('receipts.approve');

    Route::post('/receipts/{receiptId}/reject', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'rejectReceipt'])
        ->where('receiptId', '[0-9]+')
        ->name('receipts.reject');

    // ابطال / بازگردانی / حذف
    Route::post('/{id}/cancel', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'cancel'])
        ->where('id', '[0-9]+')
        ->name('cancel');

    Route::post('/{id}/restore', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'restore'])
        ->where('id', '[0-9]+')
        ->name('restore');

    Route::delete('/{id}', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'destroy'])
        ->where('id', '[0-9]+')
        ->name('destroy');

    // تغییر سریع وضعیت
    Route::post('/{id}/status', [\App\Http\Controllers\Admin\AdminTariffInvoiceController::class, 'quickChangeStatus'])
        ->where('id', '[0-9]+')
        ->name('status');
});

// ═══════════════════════════════════════════════════════════
// پنل دهیار — گزارش فیش‌های عوارض (خواندنی)
// ═══════════════════════════════════════════════════════════
Route::middleware(['web', 'auth'])->prefix('admin/village/{village}/tariff-reports')->name('admin.village.tariff-reports.')->group(function () {
    Route::get('/', [\App\Http\Controllers\VillageAdmin\TariffReportController::class, 'index'])->name('index');
    Route::get('/summary', [\App\Http\Controllers\VillageAdmin\TariffReportController::class, 'summary'])->name('summary');
    Route::get('/{id}', [\App\Http\Controllers\VillageAdmin\TariffReportController::class, 'show'])->where('id', '[0-9]+')->name('show');
});

// ═══════════════════════════════════════════════════════════
// صفحه تعرفه روستا با صدور فیش و آپلود رسید
// ═══════════════════════════════════════════════════════════
Route::prefix('village/{slug}/tariff')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\VillageTariffController::class, 'index'])
        ->name('tariff.public')
        ->middleware('alias:tariff.public');
    Route::get('/', [\App\Http\Controllers\Public\VillageTariffController::class, 'index'])
        ->name('village.tariff.index');
    Route::post('/invoice', [\App\Http\Controllers\Public\VillageTariffController::class, 'issueInvoice'])->name('invoice.issue');
    Route::post('/track', [\App\Http\Controllers\Public\VillageTariffController::class, 'track'])->name('track');
});

Route::get('/tariff/invoice/{token}', [\App\Http\Controllers\Public\VillageTariffController::class, 'showInvoice'])
    ->name('village.tariff.invoice.show');
Route::post('/tariff/invoice/{token}/receipt', [\App\Http\Controllers\Public\VillageTariffController::class, 'uploadReceipt'])
    ->name('village.tariff.invoice.receipt');


// Alias حذف شد

// ═══════════════════════════════════════════════════════════
// صفحه تعرفه روستا — صدور فیش و پیگیری
// ═══════════════════════════════════════════════════════════
Route::prefix('village/{slug}/tariff')->name('village.tariff.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Public\VillageTariffController::class, 'index'])
        ->name('index');
    Route::post('/invoice', [\App\Http\Controllers\Public\VillageTariffController::class, 'issueInvoice'])
        ->name('invoice.issue');
    Route::post('/track', [\App\Http\Controllers\Public\VillageTariffController::class, 'track'])
        ->name('track');
});

// نمایش فیش با توکن
Route::get('/tariff/invoice/{token}', [\App\Http\Controllers\Public\VillageTariffController::class, 'showInvoice'])
    ->name('village.tariff.invoice.show');

// آپلود رسید
Route::post('/tariff/invoice/{token}/receipt', [\App\Http\Controllers\Public\VillageTariffController::class, 'uploadReceipt'])
    ->name('village.tariff.invoice.receipt');

