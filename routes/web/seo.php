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
| SEO — Sitemap و RSS
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'sitemap'])
    ->name('seo.sitemap.new');

Route::get('/feed.xml', [\App\Http\Controllers\SitemapController::class, 'rss'])
    ->name('seo.rss');

/*
|--------------------------------------------------------------------------
| Global Search
|--------------------------------------------------------------------------
*/

Route::get('/search', [\App\Http\Controllers\SearchController::class, 'search'])
    ->name('search.index');

/*
|--------------------------------------------------------------------------
| Admin Export
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin/export')->group(function () {
    Route::get('/villages', [\App\Http\Controllers\AdminExportController::class, 'exportVillages'])
        ->name('admin.export.villages');
});

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/sections/{sectionId}/gallery',
        [\App\Http\Controllers\AdminSectionGalleryController::class, 'index'])
        ->name('admin.sections.gallery.index');
    Route::get('/sections/{sectionId}/gallery/create',
        [\App\Http\Controllers\AdminSectionGalleryController::class, 'create'])
        ->name('admin.sections.gallery.create');
    Route::post('/sections/{sectionId}/gallery',
        [\App\Http\Controllers\AdminSectionGalleryController::class, 'store'])
        ->name('admin.sections.gallery.store');
    Route::delete('/sections/{sectionId}/gallery/{imageId}',
        [\App\Http\Controllers\AdminSectionGalleryController::class, 'destroy'])
        ->name('admin.sections.gallery.destroy');
});
