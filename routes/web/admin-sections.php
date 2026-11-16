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
| Admin Sections Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/sections', [\App\Http\Controllers\AdminSectionController::class, 'index'])
        ->name('admin.sections.index');

    Route::get('/sections/{id}/edit', [\App\Http\Controllers\AdminSectionController::class, 'edit'])
        ->name('admin.sections.edit');

    // عکس هدر
    Route::post('sections/{id}/upload-cover', [\App\Http\Controllers\AdminSectionController::class, 'uploadCover'])->name('admin.sections.uploadCover');
    Route::delete('sections/{id}/delete-cover', [\App\Http\Controllers\AdminSectionController::class, 'deleteCover'])->name('admin.sections.deleteCover');

    // مدیریت همکاران
    Route::resource('sections/{section}/staff', \App\Http\Controllers\AdminSectionStaffController::class)->names('admin.sections.staff');


    Route::put('/sections/{id}', [\App\Http\Controllers\AdminSectionController::class, 'update'])
        ->name('admin.sections.update');

    Route::post('/sections/{id}/photo', [\App\Http\Controllers\AdminSectionController::class, 'uploadPhoto'])
        ->name('admin.sections.uploadPhoto');

    Route::delete('/sections/{id}/photo', [\App\Http\Controllers\AdminSectionController::class, 'deletePhoto'])
        ->name('admin.sections.deletePhoto');
});

/*
|--------------------------------------------------------------------------
| Admin Users Management
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/users', [\App\Http\Controllers\AdminUserController::class, 'index'])
        ->name('admin.users.index');

    Route::get('/users/create', [\App\Http\Controllers\AdminUserController::class, 'create'])
        ->name('admin.users.create');

    Route::post('/users', [\App\Http\Controllers\AdminUserController::class, 'store'])
        ->name('admin.users.store');

    Route::get('/users/{id}/edit', [\App\Http\Controllers\AdminUserController::class, 'edit'])
        ->name('admin.users.edit');

    Route::put('/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('/users/{id}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])
        ->name('admin.users.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin - روستاهای هر بخش
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/sections/{sectionId}/villages',
        [\App\Http\Controllers\AdminVillageController::class, 'bySection'])
        ->name('admin.sections.villages');

    // فعال/غیرفعال کردن گروهی همه‌ی روستاهای یک بخش
    Route::post('/sections/{sectionId}/villages/bulk-toggle',
        [\App\Http\Controllers\AdminVillageController::class, 'bulkToggle'])
        ->name('admin.sections.villages.bulk-toggle');

    Route::delete('/villages/{id}',
        [\App\Http\Controllers\AdminVillageController::class, 'destroy'])
        ->name('admin.villages.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Section News
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin/section-news')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminSectionNewsController::class, 'index'])
        ->name('admin.section-news.index');

    Route::get('/create', [\App\Http\Controllers\AdminSectionNewsController::class, 'create'])
        ->name('admin.section-news.create');

    Route::post('/', [\App\Http\Controllers\AdminSectionNewsController::class, 'store'])
        ->name('admin.section-news.store');

    Route::get('/{id}/edit', [\App\Http\Controllers\AdminSectionNewsController::class, 'edit'])
        ->name('admin.section-news.edit');

    Route::put('/{id}', [\App\Http\Controllers\AdminSectionNewsController::class, 'update'])
        ->name('admin.section-news.update');

    Route::delete('/{id}', [\App\Http\Controllers\AdminSectionNewsController::class, 'destroy'])
        ->name('admin.section-news.destroy');

    // حذف یک عکس از گالری خبر بخش
    // (این مسیر قبلاً وجود نداشت و صفحه ویرایش خبر — به‌محض داشتن عکس — خطای 500 می‌داد)
    Route::delete('/{id}/image/{imageId}', [\App\Http\Controllers\AdminSectionNewsController::class, 'destroyImage'])
        ->where('id', '[0-9]+')
        ->where('imageId', '[0-9]+')
        ->name('admin.section-news.image.destroy');
});

/*
|--------------------------------------------------------------------------
| Public Section News
|--------------------------------------------------------------------------
*/

Route::middleware('section')->get('/news', [
    \App\Http\Controllers\PublicSectionNewsController::class,
    'index'
])->name('section.news.index');

Route::middleware('section')->get('/news/{slug}', [
    \App\Http\Controllers\PublicSectionNewsController::class,
    'show'
])->name('section.news.show');
