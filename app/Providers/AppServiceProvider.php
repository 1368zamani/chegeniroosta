<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ═══ Policy های ماژول تعرفه ═══

        \App\Models\Village::observe(\App\Observers\VillageObserver::class);
        //
    }
}
