<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * آمار روزانه — برای نمودار روند بازدید.
 *
 * جدول `content_stats` فقط «مجموع کل» را نگه می‌دارد و از آن نمی‌شود
 * فهمید کدام روز چند بازدید داشته‌ایم. این جدول آمار را به تفکیک
 * «روز × محتوا» نگه می‌دارد تا:
 *   • نمودار روند ۳۰ روزه رسم شود؛
 *   • «بازدید امروز» محاسبه شود؛
 *   • و هر ادمین بخش، فقط آمار بخش خودش را ببیند (ستون section_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('content_stat_daily')) {
            return;
        }

        Schema::create('content_stat_daily', function (Blueprint $table) {
            $table->id();

            $table->date('stat_date');

            $table->string('statable_type', 191);
            $table->unsignedBigInteger('statable_id');

            // برای محدود کردن آمار به بخش/روستای ادمین
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('village_id')->nullable();

            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('downloads')->default(0);

            $table->timestamps();

            // برای هر محتوا، در هر روز فقط یک ردیف
            $table->unique(['stat_date', 'statable_type', 'statable_id'], 'content_stat_daily_unique');

            $table->index(['stat_date', 'section_id']);
            $table->index(['stat_date', 'village_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_stat_daily');
    }
};
