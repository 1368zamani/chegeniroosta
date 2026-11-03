<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * بازطراحی بخش عوارض — دسته‌بندی پسماند و ساخت‌وساز.
 *
 * ستون‌های جدید روی `tariff_titles` (کاتالوگ عناوین عوارض):
 *   category      → waste | construction
 *   subcategory   → new_build | renovation   (فقط برای ساخت‌وساز)
 *   unit          → واحد محاسبه (متر مربع، مورد، ...)
 *   description   → توضیح کوتاه برای شهروند
 *   base_amount   → مبلغ پایه‌ی پیشنهادی (ریال)
 *   effective_from→ تاریخ اعمال تعرفه
 *   updated_by    → آخرین ویرایش‌کننده
 *
 * و جدول `tariff_change_logs` برای ثبت تاریخچه‌ی کامل تغییرات.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── ۱) ستون‌های جدید روی tariff_titles ──
        Schema::table('tariff_titles', function (Blueprint $table) {
            if (! Schema::hasColumn('tariff_titles', 'category')) {
                $table->enum('category', ['waste', 'construction'])->nullable()->after('type');
            }
            if (! Schema::hasColumn('tariff_titles', 'subcategory')) {
                $table->enum('subcategory', ['new_build', 'renovation'])->nullable()->after('category');
            }
            if (! Schema::hasColumn('tariff_titles', 'unit')) {
                $table->string('unit', 30)->nullable()->after('name_fa');
            }
            if (! Schema::hasColumn('tariff_titles', 'description')) {
                $table->string('description', 500)->nullable()->after('statutory_note');
            }
            if (! Schema::hasColumn('tariff_titles', 'base_amount')) {
                $table->unsignedBigInteger('base_amount')->nullable()->after('description');
            }
            if (! Schema::hasColumn('tariff_titles', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('base_amount');
            }
            if (! Schema::hasColumn('tariff_titles', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('effective_from');
            }
        });

        // ── ۲) ایندکس‌ها (جدا، تا اگر ستون‌ها از قبل بودند خطا ندهد) ──
        Schema::table('tariff_titles', function (Blueprint $table) {
            try { $table->index(['category', 'subcategory'], 'tt_category_idx'); } catch (\Throwable $e) {}
        });

        // ── ۳) جدول تاریخچه‌ی تغییرات ──
        if (! Schema::hasTable('tariff_change_logs')) {
            Schema::create('tariff_change_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tariff_title_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name', 120)->nullable();
                $table->enum('action', [
                    'create', 'update_amount', 'update_info', 'toggle_active', 'delete', 'restore',
                ]);
                $table->unsignedBigInteger('old_amount')->nullable();
                $table->unsignedBigInteger('new_amount')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('reason', 500)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['tariff_title_id', 'created_at'], 'tcl_title_idx');
                $table->index(['user_id', 'created_at'], 'tcl_user_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_change_logs');

        Schema::table('tariff_titles', function (Blueprint $table) {
            foreach (['category', 'subcategory', 'unit', 'description', 'base_amount', 'effective_from', 'updated_by'] as $col) {
                if (Schema::hasColumn('tariff_titles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
