<?php

namespace Database\Factories;

use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Village>
 */
class VillageFactory extends Factory
{
    protected $model = Village::class;

    public function definition(): array
    {
        $suffix = fake()->unique()->numerify('######');

        return [
            'name' => 'روستای تست '.$suffix,
            'slug' => 'test-village-'.$suffix,
            'province' => 'لرستان',
            'county' => 'چگنی',
            'district' => 'مرکزی',
            'is_active' => true,

            // NOT NULL columns without database defaults.
            'has_school' => true,
            'has_health_house' => true,
            'has_mosque' => true,
            'has_water' => true,
            'has_gas' => true,
            'has_electricity' => true,
            'has_internet' => true,
            'has_sport_field' => false,

            'show_stats' => true,
            'show_news' => true,
            'show_projects' => true,
            'show_events' => true,
            'show_facilities' => true,
            'show_council' => true,
            'show_attractions' => true,
            'show_gallery' => true,
            'show_request_box' => true,
            'show_about' => true,
            'show_info' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
