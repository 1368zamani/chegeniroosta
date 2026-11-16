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
| Admin per Section (Subdomain)
|--------------------------------------------------------------------------
*/

// Admin subdomain route temporarily disabled

/*
|--------------------------------------------------------------------------
| Admin Settings (Super Admin Only)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'index'])
        ->name('admin.settings.index');
    Route::post('/settings', [\App\Http\Controllers\AdminSettingsController::class, 'update'])
        ->name('admin.settings.update');
});

/*
|--------------------------------------------------------------------------
| Admin Backups
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/backups', function () {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        $dir = storage_path('backups/auto');
        $files = [];
        if (is_dir($dir)) {
            foreach (glob($dir . '/*') as $f) {
                $files[] = [
                    'name' => basename($f),
                    'size' => filesize($f),
                    'date' => filemtime($f),
                ];
            }
            usort($files, fn($a, $b) => $b['date'] <=> $a['date']);
        }

        return view('admin.backups', compact('files'));
    })->name('admin.backups.index');

    Route::post('/backups/run', function () {
        if (!auth()->user()->isSuperAdmin()) abort(403);

        \Illuminate\Support\Facades\Artisan::call('app:backup');
        $output = \Illuminate\Support\Facades\Artisan::output();

        return back()->with('success', 'بکاپ اجرا شد.\n' . $output);
    })->name('admin.backups.run');
});
