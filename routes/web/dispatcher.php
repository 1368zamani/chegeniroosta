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
| Dispatcher اصلی برای '/'
|--------------------------------------------------------------------------
| اولویت: بخش > روستا > مرکزی
*/

Route::middleware(['section', 'village'])->get('/', function (\Illuminate\Http\Request $request) {

    if (app()->bound('currentSection')) {
        return app(\App\Http\Controllers\SectionSiteController::class)->home($request);
    }

    if (app()->bound('currentVillage')) {
        return app(\App\Http\Controllers\VillageSiteController::class)->home($request);
    }

    return app(\App\Http\Controllers\CentralController::class)->home($request);

})->name('home');

/*
|--------------------------------------------------------------------------
| Bakhshdar Page
|--------------------------------------------------------------------------
*/

Route::middleware('section')->get('/bakhshdar', [
    \App\Http\Controllers\SectionSiteController::class,
    'bakhshdar'
])->name('section.bakhshdar');
