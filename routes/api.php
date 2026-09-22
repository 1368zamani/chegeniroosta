<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SectionApiController;
use App\Http\Controllers\Api\VillageApiController;
use App\Http\Controllers\Api\NewsApiController;

/*
|--------------------------------------------------------------------------
| API Routes - v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // سلامت
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'version' => '1.0',
            'time' => now()->toIso8601String(),
        ]);
    });

    // بخش‌ها
    Route::get('/sections', [SectionApiController::class, 'index']);
    Route::get('/sections/{slug}', [SectionApiController::class, 'show']);
    Route::get('/sections/{slug}/villages', [SectionApiController::class, 'villages']);
    Route::get('/sections/{slug}/news', [SectionApiController::class, 'news']);

    // روستاها
    Route::get('/villages', [VillageApiController::class, 'index']);
    Route::get('/villages/{slug}', [VillageApiController::class, 'show']);
    Route::get('/villages/{slug}/gallery', [VillageApiController::class, 'gallery']);
    Route::get('/villages/{slug}/news', [VillageApiController::class, 'news']);

    // اخبار
    Route::get('/news', [NewsApiController::class, 'index']);
    Route::get('/news/{slug}', [NewsApiController::class, 'show']);

    // جستجو
    Route::get('/search', [VillageApiController::class, 'search']);

    // آمار کلی
    Route::get('/stats', [SectionApiController::class, 'stats']);
});

// fallback برای 404
Route::fallback(function () {
    return response()->json([
        'error' => 'Route not found',
        'code' => 404,
    ], 404);
});
