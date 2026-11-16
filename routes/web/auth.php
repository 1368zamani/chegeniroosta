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
| سایت روستا (صفحه عمومی — بدون نیاز به ورود)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| ورود / خروج
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // ⚠️ امنیت: محدودیت تلاش ورود (۱۰ تلاش در دقیقه برای هر IP).
    // قبلاً هیچ محدودیتی نبود و پسورد سوپرادمین یک عدد ۶ رقمی است،
    // یعنی با حمله‌ی فرهنگ‌نامه‌ای در چند دقیقه قابل حدس زدن بود.
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
