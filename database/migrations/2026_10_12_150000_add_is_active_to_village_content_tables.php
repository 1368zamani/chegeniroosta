<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * افزودن ستون «فعال/غیرفعال» به چهار جدول محتوایی روستا.
 *
 * قبلاً این جدول‌ها هیچ ستونی برای غیرفعال‌سازی نداشتند، بنابراین ادمین
 * نمی‌توانست مثلاً یک جاذبه یا یک پروژه را بدون حذف کردن، از سایت بردارد.
 *
 * مقدار پیش‌فرض true است، پس هیچ محتوای موجودی ناپدید نمی‌شود.
 */
return new class extends Migration
{
    private array $tables = [
        'village_attractions',
        'village_facilities',
        'village_projects',
        'village_events',
    ];

    public function up(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && !Schema::hasColumn($name, 'is_active')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->boolean('is_active')->default(true)->index();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $name) {
            if (Schema::hasTable($name) && Schema::hasColumn($name, 'is_active')) {
                Schema::table($name, function (Blueprint $table) {
                    $table->dropColumn('is_active');
                });
            }
        }
    }
};
