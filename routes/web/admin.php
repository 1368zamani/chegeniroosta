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
| پنل مدیریت
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/admin/villages/create', [\App\Http\Controllers\AdminVillageController::class, 'create'])
        ->name('admin.villages.create');

    Route::post('/admin/villages', [\App\Http\Controllers\AdminVillageController::class, 'store'])
        ->name('admin.villages.store');

    Route::get('/admin/village/{village}/images/create',
        [VillageImageController::class, 'create'])
        ->name('admin.images.create');

    Route::post('/admin/village/{village}/images',
        [VillageImageController::class, 'store'])
        ->name('admin.images.store');

    Route::get('/admin/village/{village}/contact',
        [VillageContactController::class, 'edit'])
        ->name('admin.contact.edit');

    Route::put('/admin/village/{village}/contact',
        [VillageContactController::class, 'update'])
        ->name('admin.contact.update');

});

/*
|--------------------------------------------------------------------------
| تست روستا
|--------------------------------------------------------------------------
*/

Route::middleware('village')->group(function () {
    Route::get('/__village_test', function () {
        $village = app('currentVillage');

        return response()->json([
            'status' => 'ok',
            'village' => $village->name,
            'slug' => $village->slug,
            'subdomain' => $village->subdomain,
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| ادمین‌های village
|--------------------------------------------------------------------------
*/


Route::middleware('auth')->prefix('admin')->group(function () {

    // ═══ ماژول عوارض و بهای خدمات ═══
    // مسئولین شهرستان
    Route::get('officials', [\App\Http\Controllers\AdminOfficialController::class, 'index'])
        ->name('admin.officials.index');
    Route::get('officials/create', [\App\Http\Controllers\AdminOfficialController::class, 'create'])
        ->name('admin.officials.create');
    Route::post('officials', [\App\Http\Controllers\AdminOfficialController::class, 'store'])
        ->name('admin.officials.store');
    Route::get('officials/{id}/edit', [\App\Http\Controllers\AdminOfficialController::class, 'edit'])
        ->name('admin.officials.edit');
    Route::put('officials/{id}', [\App\Http\Controllers\AdminOfficialController::class, 'update'])
        ->name('admin.officials.update');
    Route::delete('officials/{id}', [\App\Http\Controllers\AdminOfficialController::class, 'destroy'])
        ->name('admin.officials.destroy');

    // شورای بخش
    Route::get('sections/{id}/council', [\App\Http\Controllers\AdminSectionCouncilController::class, 'index'])
        ->name('admin.sections.council.index');
    Route::get('sections/{id}/council/create', [\App\Http\Controllers\AdminSectionCouncilController::class, 'create'])
        ->name('admin.sections.council.create');
    Route::post('sections/{id}/council', [\App\Http\Controllers\AdminSectionCouncilController::class, 'store'])
        ->name('admin.sections.council.store');
    Route::get('sections/{id}/council/{memberId}/edit', [\App\Http\Controllers\AdminSectionCouncilController::class, 'edit'])
        ->name('admin.sections.council.edit');
    Route::put('sections/{id}/council/{memberId}', [\App\Http\Controllers\AdminSectionCouncilController::class, 'update'])
        ->name('admin.sections.council.update');
    Route::delete('sections/{id}/council/{memberId}', [\App\Http\Controllers\AdminSectionCouncilController::class, 'destroy'])
        ->name('admin.sections.council.destroy');

    // داشبورد یکپارچه بخشداری
    Route::get('sections/{id}/dashboard', [\App\Http\Controllers\AdminSectionDashboardController::class, 'index'])
        ->name('admin.sections.dashboard');

    // درخواست‌های همه روستاهای بخش (فاز ۲)
    Route::get('sections/{id}/all-requests', [\App\Http\Controllers\AdminSectionRequestController::class, 'allRequests'])
        ->name('admin.sections.all-requests');
    Route::get('sections/{id}/all-requests/{requestId}', [\App\Http\Controllers\AdminSectionRequestController::class, 'showVillageRequest'])
        ->name('admin.sections.all-requests.show');

    // درخواست‌های مردمی بخشداری
    Route::get('sections/{id}/requests', [\App\Http\Controllers\AdminSectionRequestController::class, 'index'])
        ->name('admin.sections.requests.index');
    Route::get('sections/{id}/requests/{requestId}', [\App\Http\Controllers\AdminSectionRequestController::class, 'show'])
        ->name('admin.sections.requests.show');
    Route::put('sections/{id}/requests/{requestId}', [\App\Http\Controllers\AdminSectionRequestController::class, 'update'])
        ->name('admin.sections.requests.update');

    // فعال/غیرفعال کردن بخش و روستا (سوپرادمین)
    Route::post('sections/{id}/toggle', [\App\Http\Controllers\AdminSectionController::class, 'toggleActive'])->name('admin.sections.toggle');
    Route::post('villages/{id}/toggle', [\App\Http\Controllers\AdminVillageController::class, 'toggleActive'])->name('admin.villages.toggle');

    // فعال/غیرفعال کردن بقیه‌ی انواع محتوا با یک مسیر مشترک
    // (جاذبه، امکانات، پروژه، رویداد، شورای روستا/بخش، اخبار، مصوبات، مقامات)
    Route::post('toggle/{type}/{id}', [\App\Http\Controllers\Admin\ToggleController::class, 'toggle'])
        ->where('type', '[a-z][a-z\-]*')
        ->where('id', '[0-9]+')
        ->name('admin.toggle');

    // آمار سایت (بازدید و دانلود)
    // سوپرادمین: کل سایت — ادمین بخش: فقط بخش خودش
    Route::get('stats', [\App\Http\Controllers\Admin\StatsController::class, 'index'])
        ->name('admin.stats');

    Route::get('/villages', [\App\Http\Controllers\AdminVillageController::class, 'index'])
        ->name('admin.villages.index');

    Route::get('/villages/{village}/edit', [\App\Http\Controllers\AdminVillageController::class, 'edit'])
        ->name('admin.villages.edit');

    Route::put('/villages/{village}', [\App\Http\Controllers\AdminVillageController::class, 'update'])
        ->name('admin.villages.update');
});

Route::middleware('auth')->prefix('admin/village/{village}/images')->group(function () {
    Route::post('/{image}/cover', [\App\Http\Controllers\AdminVillageImageController::class, 'setCover'])
        ->name('admin.village.images.setCover');
});

Route::middleware('auth')->prefix('admin/village/{village}/images')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminVillageImageController::class, 'index'])
        ->name('admin.village.images.index');

    Route::get('/create', [\App\Http\Controllers\AdminVillageImageController::class, 'create'])
        ->name('admin.village.images.create');

    Route::post('/', [\App\Http\Controllers\AdminVillageImageController::class, 'store'])
        ->name('admin.village.images.store');

    Route::delete('/{image}', [\App\Http\Controllers\AdminVillageImageController::class, 'destroy'])
        ->name('admin.village.images.destroy');
});

