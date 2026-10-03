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
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

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
    Route::prefix('fees')->name('admin.fees.')->group(function () {
        // دسته‌بندی‌ها
        Route::get('categories',              [\App\Http\Controllers\Admin\FeeCategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create',       [\App\Http\Controllers\Admin\FeeCategoryController::class, 'create'])->name('categories.create');
        Route::post('categories',             [\App\Http\Controllers\Admin\FeeCategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{id}/edit',    [\App\Http\Controllers\Admin\FeeCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{id}',         [\App\Http\Controllers\Admin\FeeCategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{id}',      [\App\Http\Controllers\Admin\FeeCategoryController::class, 'destroy'])->name('categories.destroy');

        // عناوین خدمت
        Route::get('types',                   [\App\Http\Controllers\Admin\FeeTypeController::class, 'index'])->name('types.index');
        Route::get('types/create',            [\App\Http\Controllers\Admin\FeeTypeController::class, 'create'])->name('types.create');
        Route::post('types',                  [\App\Http\Controllers\Admin\FeeTypeController::class, 'store'])->name('types.store');
        Route::get('types/{id}/edit',         [\App\Http\Controllers\Admin\FeeTypeController::class, 'edit'])->name('types.edit');
        Route::put('types/{id}',              [\App\Http\Controllers\Admin\FeeTypeController::class, 'update'])->name('types.update');
        Route::delete('types/{id}',           [\App\Http\Controllers\Admin\FeeTypeController::class, 'destroy'])->name('types.destroy');
    });

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

/*
|--------------------------------------------------------------------------
| Village Site (سامانه جامع روستاها)
|--------------------------------------------------------------------------
*/

Route::prefix('village/{slug}')->group(function () {

    Route::get('/', [\App\Http\Controllers\PublicVillageSiteController::class, 'home'])
        ->name('village.site.home');

    Route::get('/news', [\App\Http\Controllers\PublicVillageSiteController::class, 'newsList'])
        ->name('village.site.news');

    Route::get('/news/{newsSlug}', [\App\Http\Controllers\PublicVillageSiteController::class, 'newsShow'])
        ->name('village.site.news.show');

    Route::get('/projects', [\App\Http\Controllers\PublicVillageSiteController::class, 'projects'])
        ->name('village.site.projects');

    Route::get('/about', [\App\Http\Controllers\PublicVillageSiteController::class, 'about'])
        ->name('village.site.about');

    Route::get('/council', [\App\Http\Controllers\PublicVillageSiteController::class, 'council'])
        ->name('village.site.council');

    Route::get('/facilities', [\App\Http\Controllers\PublicVillageSiteController::class, 'facilities'])
        ->name('village.site.facilities');

    Route::get('/events', [\App\Http\Controllers\PublicVillageSiteController::class, 'events'])
        ->name('village.site.events');

    Route::get('/request', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestCreate'])
        ->name('village.site.request.create');

    Route::post('/request', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestStore'])
        ->name('village.site.request.store');

    Route::get('/request/success/{code}', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestSuccess'])
        ->name('village.site.request.success');

    Route::get('/track', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestTrack'])
        ->name('village.site.request.track');

    Route::post('/track', [\App\Http\Controllers\PublicVillageSiteController::class, 'requestTrackResult'])
        ->name('village.site.request.track.result');
});

/*
|--------------------------------------------------------------------------
| Public Village Profile
|--------------------------------------------------------------------------
*/

Route::get('/village/{slug}', [\App\Http\Controllers\PublicVillageController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('public.village.show');

/*
|--------------------------------------------------------------------------
| Village Admin Panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'village-access'])->prefix('admin/village/{village}')->group(function () {

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
});

/*
|--------------------------------------------------------------------------
| Village Council Resolutions (Public)
|--------------------------------------------------------------------------
*/

Route::get('/village/{slug}/resolutions', [\App\Http\Controllers\PublicVillageSiteController::class, 'resolutions'])
    ->name('village.site.resolutions');

Route::get('/council', [\App\Http\Controllers\PublicSectionController::class, 'council'])
    ->middleware('section')
    ->name('section.site.council');

Route::get('/village/{slug}/dehyar', [\App\Http\Controllers\PublicVillageSiteController::class, 'dehyar'])
    ->name('village.site.dehyar');

// ═══════════════════════════════════════════════
// مسئولین شهرستان (Public)
// ═══════════════════════════════════════════════
Route::get('/officials/governor', [\App\Http\Controllers\PublicOfficialController::class, 'governor'])
    ->name('officials.governor');
Route::get('/officials/deputies', [\App\Http\Controllers\PublicOfficialController::class, 'deputies'])
    ->name('officials.deputies');
Route::get('/officials/deputy/{id}', [\App\Http\Controllers\PublicOfficialController::class, 'deputy'])
    ->name('officials.deputy');
Route::get('/officials/bakhshdars', [\App\Http\Controllers\PublicOfficialController::class, 'bakhshdars'])
    ->name('officials.bakhshdars');

/* ═══ محاسبه‌گر عمومی عوارض ═══ */
use App\Http\Controllers\Public\FeeCalculatorController;

Route::prefix('fees')->name('fees.')->group(function () {
    Route::get('/', [FeeCalculatorController::class, 'index'])->name('calculator');
    Route::post('/calculate', [FeeCalculatorController::class, 'calculate'])->name('calculate');
    Route::get('/bill/{billNumber}', [FeeCalculatorController::class, 'show'])->name('bill.show');
});

/* ═══ پرداخت دستی (فیش واریزی) ═══ */
Route::prefix('fees')->name('fees.')->group(function () {
    Route::post('/bill/{billNumber}/upload', [\App\Http\Controllers\Public\FeePaymentController::class, 'upload'])->name('bill.upload');
    Route::match(['get', 'post'], '/tracking', [\App\Http\Controllers\Public\FeePaymentController::class, 'tracking'])->name('tracking');
});

/* ═══ پنل ادمین — مدیریت عوارض ═══ */
Route::middleware('auth')->prefix('admin/fees')->name('admin.fees.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Admin\FeeAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('bills', [\App\Http\Controllers\Admin\FeeAdminController::class, 'bills'])->name('bills.index');
    Route::get('bills/{id}', [\App\Http\Controllers\Admin\FeeAdminController::class, 'billShow'])->whereNumber('id')->name('bills.show');
    Route::get('payments', [\App\Http\Controllers\Admin\FeeAdminController::class, 'payments'])->name('payments.index');
    Route::post('payments/{id}/approve', [\App\Http\Controllers\Admin\FeeAdminController::class, 'approvePayment'])->whereNumber('id')->name('payments.approve');
    Route::post('payments/{id}/reject', [\App\Http\Controllers\Admin\FeeAdminController::class, 'rejectPayment'])->whereNumber('id')->name('payments.reject');
    Route::get('tariffs', [\App\Http\Controllers\Admin\FeeAdminController::class, 'tariffs'])->name('tariffs.index');
    Route::put('tariffs/{id}', [\App\Http\Controllers\Admin\FeeAdminController::class, 'tariffUpdate'])->whereNumber('id')->name('tariffs.update');
    Route::get('accounts', [\App\Http\Controllers\Admin\FeeAdminController::class, 'accounts'])->name('accounts.index');
    Route::put('accounts/{id}', [\App\Http\Controllers\Admin\FeeAdminController::class, 'accountUpdate'])->whereNumber('id')->name('accounts.update');
    Route::get('export/bills.csv', [\App\Http\Controllers\Admin\FeeAdminController::class, 'exportBills'])->name('export.bills');
    Route::get('export/payments.csv', [\App\Http\Controllers\Admin\FeeAdminController::class, 'exportPayments'])->name('export.payments');
});

/* ═══ نمایش فایل فیش برای ادمین (محافظت‌شده) ═══ */
Route::middleware('auth')->get('/admin/fees/receipt/{id}', function (int $id) {
    $u = auth()->user();
    if (!$u || (!$u->isSuperAdmin() && !$u->isSectionAdmin())) abort(403);
    $r = \App\Models\PaymentReceipt::findOrFail($id);
    $path = storage_path('app/private/' . $r->file_path);
    if (!file_exists($path)) $path = storage_path('app/' . $r->file_path);
    abort_unless(file_exists($path), 404);
    return response()->file($path);
})->name('admin.fees.receipt');

/* ═══ ورود اختصاصی کارشناس مالی و عمرانی بخشداری ═══ */
use App\Http\Controllers\Auth\FeeOfficerLoginController;

Route::get('/fees-login',  [FeeOfficerLoginController::class, 'showLoginForm'])->name('fees.login');
Route::post('/fees-login', [FeeOfficerLoginController::class, 'login'])->name('fees.login.submit');
Route::post('/fees-logout', [FeeOfficerLoginController::class, 'logout'])->name('fees.logout');

/* ═══ مدیریت کارشناسان مالی و عمرانی (پنل بخشداری) ═══ */


/* ═══ گزارش درآمد عوارض ═══ */
Route::middleware('auth')->get('/admin/fees/reports', [\App\Http\Controllers\Admin\FeeReportController::class, 'index'])->name('admin.fees.reports.index');

/* ═══ sitemap-news برای Google News ═══ */
Route::get('/sitemap-news.xml', [\App\Http\Controllers\SitemapController::class, 'sitemapNews'])
    ->name('seo.sitemap.news');

/* ═══ مدیریت رمزهای کاربران (سوپرادمین + بخشدار) ═══ */
Route::middleware('auth')->prefix('admin/passwords')->name('admin.passwords.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PasswordManagerController::class, 'index'])->name('index');
    Route::post('/{id}/reset', [\App\Http\Controllers\Admin\PasswordManagerController::class, 'reset'])->whereNumber('id')->name('reset');
    Route::post('/bulk-reset', [\App\Http\Controllers\Admin\PasswordManagerController::class, 'bulkReset'])->name('bulk-reset');
    Route::get('/generate', [\App\Http\Controllers\Admin\PasswordManagerController::class, 'generate'])->name('generate');
});

/* ═══ مدیریت کارشناسان مالی و فنی/عمرانی ═══ */
Route::middleware('auth')->prefix('admin/fees/officers')->name('admin.fees.officers.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\FeeOfficerController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\FeeOfficerController::class, 'store'])->name('store');
    Route::put('/{id}', [\App\Http\Controllers\Admin\FeeOfficerController::class, 'update'])->whereNumber('id')->name('update');
    Route::put('/{id}/password', [\App\Http\Controllers\Admin\FeeOfficerController::class, 'updatePassword'])->whereNumber('id')->name('password');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\FeeOfficerController::class, 'destroy'])->whereNumber('id')->name('destroy');
});

/* ═══ ایجاد کاربر جدید ═══ */
Route::middleware('auth')->post('/admin/passwords/store', [\App\Http\Controllers\Admin\PasswordManagerController::class, 'store'])
    ->name('admin.passwords.store');
