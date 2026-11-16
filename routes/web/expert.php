<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VillageContactController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\VillageImageController;
use App\Http\Controllers\VillageNewsController;
use Illuminate\Support\Facades\Route;


// ═══════════════════════════════════════════════════════════
// پنل کارشناسان فنی و مالی (Expert Panel)
// ═══════════════════════════════════════════════════════════
Route::prefix('expert')->name('expert.')->group(function () {
    // ورود و خروج
    Route::get('/login',  [\App\Http\Controllers\Expert\ExpertAuthController::class, 'showLogin'])->name('login');
    // ⚠️ امنیت: محدودیت تلاش ورود پنل کارشناسان
    Route::post('/login', [\App\Http\Controllers\Expert\ExpertAuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.post');
    Route::post('/logout',[\App\Http\Controllers\Expert\ExpertAuthController::class, 'logout'])->name('logout');

    // صفحات محافظت‌شده
    Route::middleware(['web', \App\Http\Middleware\EnsureExpertAccess::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Expert\ExpertDashboardController::class, 'index'])->name('dashboard');

        // ═══ تعرفه‌ها — از کنترلر ادمین استفاده می‌کنیم ═══
        Route::get('/tariffs', [\App\Http\Controllers\Admin\TariffSetController::class, 'index'])
            ->name('tariffs.index');
        Route::get('/tariffs/{tariffSet}', [\App\Http\Controllers\Admin\TariffSetController::class, 'show'])
            ->where('tariffSet', '[0-9]+')
            ->name('tariffs.show');
        Route::get('/tariffs/{tariffSet}/edit', [\App\Http\Controllers\Admin\TariffSetController::class, 'edit'])
            ->where('tariffSet', '[0-9]+')
            ->name('tariffs.edit');

        // ═══ آیتم‌ها ═══
        Route::get('/items', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'items'])->name('items.index');
        Route::get('/items/{id}/edit', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'editItem'])->where('id','[0-9]+')->name('items.edit');
        Route::put('/items/{id}', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'updateItem'])->where('id','[0-9]+')->name('items.update');

        // ═══ مفروضات ═══
        Route::get('/parameters', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'parameters'])->name('parameters.index');
        Route::get('/parameters/{id}/edit', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'editParameter'])->where('id','[0-9]+')->name('parameters.edit');
        Route::put('/parameters/{id}', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'updateParameter'])->where('id','[0-9]+')->name('parameters.update');

        // ═══ معافیت‌ها ═══
        Route::get('/exemptions', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'exemptions'])->name('exemptions.index');
        Route::get('/exemptions/{id}/edit', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'editExemption'])->where('id','[0-9]+')->name('exemptions.edit');
        Route::put('/exemptions/{id}', [\App\Http\Controllers\Expert\ExpertTariffContentController::class, 'updateExemption'])->where('id','[0-9]+')->name('exemptions.update');

        // ═══ فیش‌های عوارض در پنل کارشناسان ═══
        Route::prefix('tariff-invoices')->name('tariff-invoices.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'index'])->name('index');
            Route::get('/account', [\App\Http\Controllers\Expert\ExpertBankAccountController::class, 'edit'])->name('account');
            Route::post('/account', [\App\Http\Controllers\Expert\ExpertBankAccountController::class, 'save'])->name('account.save');
            Route::get('/{id}', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'show'])->where('id','[0-9]+')->name('show');
            Route::post('/receipts/{receiptId}/approve', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'approveReceipt'])->where('receiptId','[0-9]+')->name('receipts.approve');
            Route::post('/receipts/{receiptId}/reject', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'rejectReceipt'])->where('receiptId','[0-9]+')->name('receipts.reject');
            Route::post('/{id}/cancel', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'cancel'])->where('id','[0-9]+')->name('cancel');
            Route::post('/{id}/restore', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'restore'])->where('id','[0-9]+')->name('restore');
            Route::delete('/{id}', [\App\Http\Controllers\Expert\ExpertTariffInvoiceController::class, 'destroy'])->where('id','[0-9]+')->name('destroy');
        });
    });
});