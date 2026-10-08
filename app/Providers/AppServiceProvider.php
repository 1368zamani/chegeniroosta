<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\TariffSet;
use App\Models\User;
use App\Policies\TariffSetPolicy;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Registry در همان جای قبلی (پایین در boot)
    }

    public function boot(): void
    {
        // ═══ Policy برای مدل TariffSet ═══
        Gate::policy(TariffSet::class, TariffSetPolicy::class);

        // ═══ Gate برای canEditType (بدون Model) ═══
        Gate::define('tariff.canEditType', function (User $user, string $type) {
            // Super/section/village admin: همه چیز
            if (in_array($user->role, ['super_admin', 'section_admin', 'village_admin'], true)) {
                return true;
            }

            // کارشناس فنی/مالی
            if ($user->role === 'fee_officer') {
                return match ($user->officer_type) {
                    'technical' => $type === 'levy',
                    'financial' => $type === 'service_fee',
                    default     => false,
                };
            }

            return false;
        });

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

            // فرمول‌های جدید
            $r->register('per_square_meter',       \App\Services\Tariff\Calculators\PerSquareMeterCalculator::class);
            $r->register('per_unit',               \App\Services\Tariff\Calculators\PerUnitCalculator::class);
            $r->register('fixed_amount',           \App\Services\Tariff\Calculators\FixedAmountCalculator::class);
            $r->register('percentage_of_value',    \App\Services\Tariff\Calculators\PercentageOfValueCalculator::class);
            $r->register('area_with_coefficient',  \App\Services\Tariff\Calculators\AreaWithCoefficientCalculator::class);
            $r->register('per_person',             \App\Services\Tariff\Calculators\PerPersonCalculator::class);

            return $r;
        });

        // Observer
        \App\Models\Village::observe(\App\Observers\VillageObserver::class);
    }
}
