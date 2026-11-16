<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VillageContactController;
use App\Http\Controllers\VillageController;
use App\Http\Controllers\VillageImageController;
use App\Http\Controllers\VillageNewsController;
use Illuminate\Support\Facades\Route;

/* ═══════════════════════════════════════════════════════════
| دانلود عمومی فایل‌ها — با شمارش تعداد دانلود
| ═══════════════════════════════════════════════════════════ */

// PDF رسمی تعرفه‌ی منتشرشده (فرم ۲ و فرم ۳)
Route::get('/download/tariff-pdf/{tariffSet}/{form?}', [\App\Http\Controllers\DownloadController::class, 'tariffPdf'])
    ->where('tariffSet', '[0-9]+')
    ->where('form', 'form[0-9]+')
    ->name('download.tariff-pdf');

// فایل پیوست محتوای عمومی (مصوبات شورای اسلامی)
Route::get('/download/{type}/{id}', [\App\Http\Controllers\DownloadController::class, 'file'])
    ->where('type', '[a-z][a-z\-]*')
    ->where('id', '[0-9]+')
    ->name('download.file');
