<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $villages = [
            [
                'name' => 'سبزوار',
                'slug' => 'sabzevar',
                'county' => 'چگنی',
                'section' => 'ویسیان',
                'district' => null,
                'province' => 'لرستان',
                'description' => 'روستای سبزوار، بخش ویسیان، شهرستان چگنی، استان لرستان.',
                'is_active' => true,
            ],
            [
                'name' => 'چم‌دیوان',
                'slug' => 'cham-divan',
                'county' => 'چگنی',
                'section' => 'ویسیان',
                'district' => null,
                'province' => 'لرستان',
                'description' => 'روستای چم‌دیوان، بخش ویسیان، شهرستان چگنی، استان لرستان.',
                'is_active' => true,
            ],
            [
                'name' => 'ابوذر',
                'slug' => 'abuzar',
                'county' => 'چگنی',
                'section' => 'ویسیان',
                'district' => null,
                'province' => 'لرستان',
                'description' => 'روستای ابوذر، بخش ویسیان، شهرستان چگنی، استان لرستان.',
                'is_active' => true,
            ],
        ];

        foreach ($villages as $village) {
            DB::table('villages')->updateOrInsert(
                ['slug' => $village['slug']],
                array_merge($village, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
