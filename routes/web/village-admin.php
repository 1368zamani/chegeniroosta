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
| Village Admin Panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'village-access'])->prefix('admin/village/{village}')->group(function () {
    // 📘 راهنمای پنل
    Route::get('/guide', function (\App\Models\Village $village) {
        return view('admin.village.guide.index', compact('village'));
    })->name('admin.village.guide');


    // مصوبات شورای اسلامی
    Route::get('/resolutions', [\App\Http\Controllers\VillageAdmin\ResolutionController::class, 'index'])
        ->name('admin.village.resolutions.index');
    Route::get('/resolutions/create', [\App\Http\Controllers\VillageAdmin\ResolutionController::class, 'create'])
        ->name('admin.village.resolutions.create');
    Route::post('/resolutions', [\App\Http\Controllers\VillageAdmin\ResolutionController::class, 'store'])
        ->name('admin.village.resolutions.store');
    Route::get('/resolutions/{id}/edit', [\App\Http\Controllers\VillageAdmin\ResolutionController::class, 'edit'])
        ->name('admin.village.resolutions.edit');
    Route::put('/resolutions/{id}', [\App\Http\Controllers\VillageAdmin\ResolutionController::class, 'update'])
        ->name('admin.village.resolutions.update');
    Route::delete('/resolutions/{id}', [\App\Http\Controllers\VillageAdmin\ResolutionController::class, 'destroy'])
        ->name('admin.village.resolutions.destroy');

    Route::get('/requests', [\App\Http\Controllers\VillageAdmin\RequestController::class, 'index'])
        ->name('admin.village.requests.index');
    Route::get('/requests/{id}', [\App\Http\Controllers\VillageAdmin\RequestController::class, 'show'])
        ->name('admin.village.requests.show');
    Route::put('/requests/{id}', [\App\Http\Controllers\VillageAdmin\RequestController::class, 'update'])
        ->name('admin.village.requests.update');

    // داشبورد
    Route::get('/', [\App\Http\Controllers\VillageAdmin\DashboardController::class, 'index'])
        ->name('admin.village.dashboard');

    // تنظیمات نمایش
    Route::get('/settings', [\App\Http\Controllers\VillageAdmin\SettingsController::class, 'index'])
        ->name('admin.village.settings.index');
    Route::post('/settings', [\App\Http\Controllers\VillageAdmin\SettingsController::class, 'update'])
        ->name('admin.village.settings.update');

    // اخبار
    Route::prefix('news')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'index'])
            ->name('admin.village.news.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'create'])
            ->name('admin.village.news.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'store'])
            ->name('admin.village.news.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'edit'])
            ->name('admin.village.news.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'update'])
            ->name('admin.village.news.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'destroy'])
            ->name('admin.village.news.destroy');

        Route::delete('/{id}/image/{imageId}', [\App\Http\Controllers\VillageAdmin\NewsController::class, 'destroyImage'])
            ->name('admin.village.news.image.destroy');
    });

    // ─── فایل‌های قابل دانلود (نمایش در پایین صفحه‌ی درخواست‌ها) ───
    Route::prefix('downloads')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\DownloadController::class, 'index'])
            ->name('admin.village.downloads.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\DownloadController::class, 'create'])
            ->name('admin.village.downloads.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\DownloadController::class, 'store'])
            ->name('admin.village.downloads.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\DownloadController::class, 'edit'])
            ->name('admin.village.downloads.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\DownloadController::class, 'update'])
            ->name('admin.village.downloads.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\DownloadController::class, 'destroy'])
            ->name('admin.village.downloads.destroy');
    });

    // ─── محصولات روستا (جهت فروش) ───
    Route::prefix('products')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'index'])
            ->name('admin.village.products.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'create'])
            ->name('admin.village.products.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'store'])
            ->name('admin.village.products.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'edit'])
            ->name('admin.village.products.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'update'])
            ->name('admin.village.products.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'destroy'])
            ->name('admin.village.products.destroy');

        Route::post('/{id}/toggle', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'toggleActive'])
            ->name('admin.village.products.toggle');
        Route::delete('/{id}/image/{imageId}', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'destroyImage'])
            ->name('admin.village.products.image.destroy');
        Route::delete('/{id}/file/{fileId}', [\App\Http\Controllers\VillageAdmin\ProductController::class, 'destroyFile'])
            ->name('admin.village.products.file.destroy');
    });

    // پروژه‌ها
    Route::prefix('projects')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\ProjectController::class, 'index'])
            ->name('admin.village.projects.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\ProjectController::class, 'create'])
            ->name('admin.village.projects.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\ProjectController::class, 'store'])
            ->name('admin.village.projects.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\ProjectController::class, 'edit'])
            ->name('admin.village.projects.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\ProjectController::class, 'update'])
            ->name('admin.village.projects.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\ProjectController::class, 'destroy'])
            ->name('admin.village.projects.destroy');
    });

    // رویدادها
    Route::prefix('events')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\EventController::class, 'index'])
            ->name('admin.village.events.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\EventController::class, 'create'])
            ->name('admin.village.events.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\EventController::class, 'store'])
            ->name('admin.village.events.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\EventController::class, 'edit'])
            ->name('admin.village.events.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\EventController::class, 'update'])
            ->name('admin.village.events.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\EventController::class, 'destroy'])
            ->name('admin.village.events.destroy');
    });

    // امکانات
    Route::prefix('facilities')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\FacilityController::class, 'index'])
            ->name('admin.village.facilities.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\FacilityController::class, 'create'])
            ->name('admin.village.facilities.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\FacilityController::class, 'store'])
            ->name('admin.village.facilities.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\FacilityController::class, 'edit'])
            ->name('admin.village.facilities.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\FacilityController::class, 'update'])
            ->name('admin.village.facilities.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\FacilityController::class, 'destroy'])
            ->name('admin.village.facilities.destroy');
    });

    // شورا
    Route::prefix('council')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\CouncilController::class, 'index'])
            ->name('admin.village.council.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\CouncilController::class, 'create'])
            ->name('admin.village.council.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\CouncilController::class, 'store'])
            ->name('admin.village.council.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\CouncilController::class, 'edit'])
            ->name('admin.village.council.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\CouncilController::class, 'update'])
            ->name('admin.village.council.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\CouncilController::class, 'destroy'])
            ->name('admin.village.council.destroy');
    });

    // جاذبه‌ها
    Route::prefix('attractions')->group(function () {
        Route::get('/', [\App\Http\Controllers\VillageAdmin\AttractionController::class, 'index'])
            ->name('admin.village.attractions.index');
        Route::get('/create', [\App\Http\Controllers\VillageAdmin\AttractionController::class, 'create'])
            ->name('admin.village.attractions.create');
        Route::post('/', [\App\Http\Controllers\VillageAdmin\AttractionController::class, 'store'])
            ->name('admin.village.attractions.store');
        Route::get('/{id}/edit', [\App\Http\Controllers\VillageAdmin\AttractionController::class, 'edit'])
            ->name('admin.village.attractions.edit');
        Route::put('/{id}', [\App\Http\Controllers\VillageAdmin\AttractionController::class, 'update'])
            ->name('admin.village.attractions.update');
        Route::delete('/{id}', [\App\Http\Controllers\VillageAdmin\AttractionController::class, 'destroy'])
            ->name('admin.village.attractions.destroy');
    });
});
