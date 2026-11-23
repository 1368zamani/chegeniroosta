<?php

use App\Http\Controllers\Admin\LevyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| زیرسامانهٔ جامع عوارض احداث بنا (بر اساس فایل اکسل مرجع)
|--------------------------------------------------------------------------
| جایگزین کامل ماژول قدیمی tariff_*
|
| سطوح دسترسی:
|   levy.report  ← گزارشها (ادمین روستا فقط همین را دارد)
|   levy.view    ← مشاهدهٔ صفحات کاری
|   levy.manage  ← ویرایش اعداد پروندهها و ضرایب (مسئول عمرانی)
|   levy.review  ← تأیید/رد رسیدها (عمرانی + مالی + managing)
*/
Route::middleware(['web', 'auth'])->prefix('admin/levy')->name('admin.levy.')->group(function () {

    // ── گزارشها: در دسترس تمام کاربران مجاز از جمله ادمین روستا ──
    Route::middleware('can:levy.report')->group(function () {
        Route::get('/reports/village/{file}', [LevyController::class, 'reportsVillage'])->name('reports.village');
        Route::get('/reports/all',            [LevyController::class, 'reportsAll'])->name('reports.all');
        Route::get('/reports/compare',        [LevyController::class, 'reportsCompare'])->name('reports.compare');
        Route::get('/reports/incomplete',     [LevyController::class, 'reportsIncomplete'])->name('reports.incomplete');
    });

    // ── مشاهدهٔ صفحات کاری (فقط خواندن) ──
    Route::middleware('can:levy.view')->group(function () {
        Route::get('/',                    [LevyController::class, 'dashboard'])->name('dashboard');
        Route::get('/villages',            [LevyController::class, 'villages'])->name('villages');
        Route::get('/accounts',            [LevyController::class, 'accounts'])->name('accounts');

        Route::get('/files',               [LevyController::class, 'filesIndex'])->name('files.index');
        Route::get('/files/create',        [LevyController::class, 'filesCreate'])->name('files.create');
        Route::get('/files/{file}',        [LevyController::class, 'filesShow'])->name('files.show');
        Route::get('/files/{file}/audit',  [LevyController::class, 'filesAudit'])->name('files.audit');

        Route::get('/export/excel',        [LevyController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/excel/{file}', [LevyController::class, 'exportFile'])->name('export.file');

        Route::get('/requests', [\App\Http\Controllers\Admin\LevyRequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{levyRequest}', [\App\Http\Controllers\Admin\LevyRequestController::class, 'show'])->name('requests.show');
    });

    // ── ویرایش اعداد اکسل و ضرایب روستاها: فقط مسئول عمرانی + managing ──
    Route::middleware('can:levy.manage')->group(function () {
        Route::put('/villages/{param}',    [LevyController::class, 'paramsUpdate'])->name('villages.update');

        Route::post('/files',              [LevyController::class, 'filesStore'])->name('files.store');
        Route::put('/files/{file}',        [LevyController::class, 'filesUpdate'])->name('files.update');
        Route::delete('/files/{file}',     [LevyController::class, 'filesDestroy'])->name('files.destroy');
        Route::post('/files/{file}/autosave', [LevyController::class, 'filesAutosave'])->name('files.autosave');
    });

    // ── تأیید/رد رسید پرداخت: عمرانی + مالی + managing ──
    Route::middleware('can:levy.review')->group(function () {
        Route::post('/requests/{levyRequest}/approve', [\App\Http\Controllers\Admin\LevyRequestController::class, 'approve'])->name('requests.approve');
        Route::post('/requests/{levyRequest}/reject',  [\App\Http\Controllers\Admin\LevyRequestController::class, 'reject'])->name('requests.reject');
    });
});
