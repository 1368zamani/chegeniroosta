<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VillageContactController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\VillageImageController;
use App\Http\Controllers\VillageNewsController;
use Illuminate\Support\Facades\Route;

/* ═══════════════════════════════════════════════════
   ماژول تعرفه عوارض و بهای خدمات (فاز ۱)
   ═══════════════════════════════════════════════════ */
/* ═══ صفحه عمومی تعرفه روستا ═══ */
/* ═══ فرم ۲ و ۳ تعرفه ═══ */
/* ═══ هدایت به تعرفه رسمی روستا ═══ */

// ═══ ماژول تعرفه عوارض و بهای خدمات ═══
// ═══ Route قدیمی حذف شد — جایگزین: village.tariff.index با alias tariff.public ═══

Route::middleware(['auth'])
    ->prefix('admin/tariffs')->name('admin.tariffs.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('create');
        Route::post('/', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('store');
        Route::get('/{tariffSet}', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('show');
        Route::get('/{tariffSet}/edit', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('edit');
        Route::put('/{tariffSet}', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('update');
        Route::post('/{tariffSet}/items', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('items.update');
        Route::post('/{tariffSet}/transition/{to}', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('transition');
        Route::delete('/{tariffSet}', [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('destroy');
    });

// ═══ ماژول «عوارض پسماند و ساخت‌وساز» — مدیریت کارشناس عمران ═══
// دسترسی واقعی در Gate های works-fees.view / works-fees.manage بررسی می‌شود
Route::middleware(['auth'])
    ->prefix('admin/works-fees')->name('admin.works-fees.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WorksFeeController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\WorksFeeController::class, 'store'])->name('store');
        Route::put('/items/{title}', [\App\Http\Controllers\Admin\WorksFeeController::class, 'update'])->name('update');
        Route::patch('/items/{title}/amount', [\App\Http\Controllers\Admin\WorksFeeController::class, 'updateAmount'])->name('amount');
        Route::post('/items/{title}/toggle', [\App\Http\Controllers\Admin\WorksFeeController::class, 'toggle'])->name('toggle');
        Route::get('/items/{title}/logs', [\App\Http\Controllers\Admin\WorksFeeController::class, 'logs'])->name('logs');
    });

// ماشین‌حساب عمومی (بدون احراز هویت)
Route::post('/village/{slug}/levy/calculate',
    [\App\Http\Controllers\Public\PublicLevyController::class, 'calculate'])
    ->name('tariff.public.calculate');

// فرم‌های چاپی تعرفه
Route::middleware(['auth'])
    ->prefix('admin/tariffs')->name('admin.tariffs.')
    ->group(function () {
        Route::get('/{tariffSet}/print/form2', [\App\Http\Controllers\Admin\TariffPrintController::class, 'form2'])->name('print.form2');
        Route::get('/{tariffSet}/print/form3', [\App\Http\Controllers\Admin\TariffPrintController::class, 'form3'])->name('print.form3');
    });

// معافیت‌های تعرفه
Route::middleware(['auth'])
    ->prefix('admin/tariffs')->name('admin.tariffs.')
    ->group(function () {
        Route::get('/{tariffSet}/exemptions',
            [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('exemptions.index');
        Route::post('/{tariffSet}/exemptions',
            [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('exemptions.store');
        Route::put('/{tariffSet}/exemptions/{exemption}',
            [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('exemptions.update');
        Route::delete('/{tariffSet}/exemptions/{exemption}',
            [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('exemptions.destroy');
    });

// لاگ‌های workflow تعرفه
Route::middleware(['auth'])
    ->prefix('admin/tariffs')->name('admin.tariffs.')
    ->group(function () {
        Route::get('/{tariffSet}/logs',
            [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('logs.index');
    });

// PDF فرم‌های تعرفه
Route::middleware(['auth'])
    ->prefix('admin/tariffs')->name('admin.tariffs.')
    ->group(function () {
        Route::get('/{tariffSet}/pdf/form2', [\App\Http\Controllers\Admin\TariffPdfController::class, 'form2'])->name('pdf.form2');
        Route::get('/{tariffSet}/pdf/form3', [\App\Http\Controllers\Admin\TariffPdfController::class, 'form3'])->name('pdf.form3');
    });

// ═══ ورود اختصاصی کارشناسان عوارض ═══
// ═══ Redirect قدیمی به پنل جدید کارشناسان ═══
Route::get('/tariff-officer/login', function () {
    return redirect()->route('expert.login');
})->name('tariff.officer.login');
Route::post('/tariff-officer/login', function () {
    return redirect()->route('expert.login');
})->name('tariff.officer.login.post');
Route::post('/tariff-officer/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('expert.login');
})->name('tariff.officer.logout');

// ═══ مدیریت کارشناسان عوارض (فقط super_admin) ═══
Route::middleware(['auth'])->prefix('admin/tariff-officers')->name('admin.tariff-officers.')->group(function () {
    Route::get('/',              [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('index');
    Route::post('/',             [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('store');
    Route::put('/{officer}',     [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('update');
    Route::delete('/{officer}',  [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('destroy');
    Route::post('/{officer}/reset-password',
        [\App\Http\Controllers\LegacyLevyController::class, 'index'])->name('reset-password');
});