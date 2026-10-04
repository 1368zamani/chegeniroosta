<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ستون‌های تاریخ شمسی را به string تبدیل می‌کنیم
        // (چون در ستون date ذخیره شمسی اشتباه است)
        Schema::table('tariff_sets', function (Blueprint $table) {
            $table->dropColumn(['deadline_approval', 'deadline_announcement']);
        });

        Schema::table('tariff_sets', function (Blueprint $table) {
            $table->string('deadline_approval', 10)->nullable()->after('created_by');
            $table->string('deadline_announcement', 10)->nullable()->after('deadline_approval');
        });
    }

    public function down(): void
    {
        Schema::table('tariff_sets', function (Blueprint $table) {
            $table->dropColumn(['deadline_approval', 'deadline_announcement']);
        });

        Schema::table('tariff_sets', function (Blueprint $table) {
            $table->date('deadline_approval')->nullable()->after('created_by');
            $table->date('deadline_announcement')->nullable()->after('deadline_approval');
        });
    }
};
