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
| Section Requests (درخواست‌های مردمی بخشداری)
|--------------------------------------------------------------------------
*/

Route::middleware(['section'])->group(function () {
    Route::get('/request', [\App\Http\Controllers\PublicSectionRequestController::class, 'requestCreate'])
        ->name('section.site.request.create');
    Route::post('/request', [\App\Http\Controllers\PublicSectionRequestController::class, 'requestStore'])
        ->name('section.site.request.store');
    Route::get('/request/success/{code}', [\App\Http\Controllers\PublicSectionRequestController::class, 'requestSuccess'])
        ->name('section.site.request.success');
    Route::get('/track', [\App\Http\Controllers\PublicSectionRequestController::class, 'requestTrack'])
        ->name('section.site.request.track');
    Route::post('/track', [\App\Http\Controllers\PublicSectionRequestController::class, 'requestTrackResult'])
        ->name('section.site.request.track.result');

// تعرفه عوارض روستاهای بخش
Route::get('/tariff', [\App\Http\Controllers\PublicSectionTariffController::class, 'index'])
    ->name('section.site.tariff');

});
