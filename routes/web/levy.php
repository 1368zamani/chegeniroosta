<?php

use App\Http\Controllers\Admin\LevyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| زیرسامانهٔ جامع عوارض احداث بنا (بر اساس فایل اکسل مرجع)
|--------------------------------------------------------------------------
| جایگزین کامل ماژول قدیمی tariff_*
*/
Route::middleware(['web', 'auth'])->prefix('admin/levy')->name('admin.levy.')->group(function () {
    Route::get('/',                    [LevyController::class, 'dashboard'])->name('dashboard');
    Route::get('/villages',            [LevyController::class, 'villages'])->name('villages');
    Route::put('/villages/{param}',    [LevyController::class, 'paramsUpdate'])->name('villages.update');
    Route::get('/accounts',            [LevyController::class, 'accounts'])->name('accounts');

    Route::get('/files',               [LevyController::class, 'filesIndex'])->name('files.index');
    Route::get('/files/create',        [LevyController::class, 'filesCreate'])->name('files.create');
    Route::post('/files',              [LevyController::class, 'filesStore'])->name('files.store');
    Route::get('/files/{file}',        [LevyController::class, 'filesShow'])->name('files.show');
    Route::put('/files/{file}',        [LevyController::class, 'filesUpdate'])->name('files.update');
    Route::delete('/files/{file}',     [LevyController::class, 'filesDestroy'])->name('files.destroy');
    Route::post('/files/{file}/autosave', [LevyController::class, 'filesAutosave'])->name('files.autosave');
    Route::get('/files/{file}/audit',  [LevyController::class, 'filesAudit'])->name('files.audit');

    Route::get('/reports/village/{file}', [LevyController::class, 'reportsVillage'])->name('reports.village');
    Route::get('/reports/all',        [LevyController::class, 'reportsAll'])->name('reports.all');
    Route::get('/reports/compare',    [LevyController::class, 'reportsCompare'])->name('reports.compare');
    Route::get('/reports/incomplete', [LevyController::class, 'reportsIncomplete'])->name('reports.incomplete');

    Route::get('/export/excel',        [LevyController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/excel/{file}', [LevyController::class, 'exportFile'])->name('export.file');
});
