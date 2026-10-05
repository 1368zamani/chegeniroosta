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
        \Illuminate\Support\Facades\Gate::policy(\App\Models\TariffSet::class, \App\Policies\TariffSetPolicy::class);

        // ═══ Registry محاسبه‌گرهای تعرفه ═══
        $this->app->singleton(\App\Services\Tariff\Calculators\CalculatorRegistry::class, function () {
            $r = new \App\Services\Tariff\Calculators\CalculatorRegistry();
            $r->register('annual_property', \App\Services\Tariff\Calculators\AnnualPropertyCalculator::class);
            return $r;
        });


        // ═══ Policy های ماژول تعرفه ═══

        \App\Models\Village::observe(\App\Observers\VillageObserver::class);
        //
    }
}
