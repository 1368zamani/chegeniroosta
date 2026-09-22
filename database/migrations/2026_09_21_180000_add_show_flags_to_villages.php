<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $showFlags = [
                'show_stats'         => 'نمایش کارت‌های آمار',
                'show_news'          => 'نمایش اخبار',
                'show_projects'      => 'نمایش پروژه‌ها',
                'show_events'        => 'نمایش رویدادها',
                'show_facilities'    => 'نمایش امکانات',
                'show_council'       => 'نمایش شورا',
                'show_attractions'   => 'نمایش جاذبه‌ها',
                'show_gallery'       => 'نمایش گالری تصاویر',
                'show_request_box'   => 'نمایش باکس درخواست مردمی',
                'show_about'         => 'نمایش بخش معرفی',
                'show_info'          => 'نمایش اطلاعات روستا',
            ];
            foreach ($showFlags as $field => $label) {
                if (!Schema::hasColumn('villages', $field)) {
                    $table->boolean($field)->default(true);
                }
            }
        });
    }

    public function down(): void {}
};
