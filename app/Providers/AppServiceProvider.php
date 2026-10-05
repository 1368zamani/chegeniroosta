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
            $r->register('annual_property',   \App\Services\Tariff\Calculators\AnnualPropertyCalculator::class);
            $r->register('sub_building',       \App\Services\Tariff\Calculators\SubBuildingCalculator::class);
            $r->register('fence_permit',       \App\Services\Tariff\Calculators\FenceCalculator::class);
            $r->register('tree_cutting',       \App\Services\Tariff\Calculators\TreeCuttingCalculator::class);
            $r->register('businesses',         \App\Services\Tariff\Calculators\BusinessCalculator::class);
            $r->register('permit_renewal',     \App\Services\Tariff\Calculators\PermitRenewalCalculator::class);
            $r->register('value_added',        \App\Services\Tariff\Calculators\ValueAddedCalculator::class);
            $r->register('value_added_zoning', \App\Services\Tariff\Calculators\ValueAddedCalculator::class);
            $r->register('value_added_development', \App\Services\Tariff\Calculators\ValueAddedCalculator::class);
            $r->register('machinery_hourly',   \App\Services\Tariff\Calculators\MachineryCalculator::class);
            $r->register('excavation',         \App\Services\Tariff\Calculators\ExcavationCalculator::class);
            $r->register('waste_residential',  \App\Services\Tariff\Calculators\WasteCalculator::class);
            $r->register('waste_commercial',   \App\Services\Tariff\Calculators\WasteCalculator::class);
            $r->register('waste_industrial',   \App\Services\Tariff\Calculators\WasteCalculator::class);
            $r->register('article_99_penalty', \App\Services\Tariff\Calculators\Article99PenaltyCalculator::class);
            return $r;
        });


        // ═══ Policy های ماژول تعرفه ═══

        \App\Models\Village::observe(\App\Observers\VillageObserver::class);
        //
    }
}
