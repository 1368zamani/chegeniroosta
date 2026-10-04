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
        \Illuminate\Support\Facades\Gate::policy(\App\Models\TariffSet::class, \App\Policies\TariffSetPolicy::class);

        \App\Models\Village::observe(\App\Observers\VillageObserver::class);
        //
    }
}
