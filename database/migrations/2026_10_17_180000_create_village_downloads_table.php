<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * بخش «دانلود فایل» برای هر روستا.
 *
 * هدف: ادمین روستا می‌تواند فایل‌ها (فرم، اطلاعیه، آیین‌نامه، عکس…)
 * را بارگذاری کند و در پایین صفحه‌ی «ثبت درخواست» شهروند نمایش داده شوند.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('village_downloads')) {
            return;
        }

        Schema::create('village_downloads', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('village_id');

            $table->string('title', 200);
            $table->string('description', 500)->nullable();

            // مسیر فایل در storage/app/public + نام اصلی و حجم
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type', 120)->nullable();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            // شمارنده‌ی دانلود
            $table->unsignedInteger('download_count')->default(0);

            $table->timestamps();

            $table->index(['village_id', 'is_active', 'sort_order'], 'vd_village_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_downloads');
    }
};
