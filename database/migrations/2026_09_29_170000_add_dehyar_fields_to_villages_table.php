<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            if (!Schema::hasColumn('villages', 'dehyar_phone')) {
                $table->string('dehyar_phone', 50)->nullable()->after('dehyar_name');
            }
            if (!Schema::hasColumn('villages', 'dehyar_email')) {
                $table->string('dehyar_email', 100)->nullable()->after('dehyar_phone');
            }
            if (!Schema::hasColumn('villages', 'dehyar_bio')) {
                $table->text('dehyar_bio')->nullable()->after('dehyar_email');
            }
            if (!Schema::hasColumn('villages', 'dehyar_education')) {
                $table->text('dehyar_education')->nullable()->after('dehyar_bio');
            }
            if (!Schema::hasColumn('villages', 'dehyar_experience')) {
                $table->text('dehyar_experience')->nullable()->after('dehyar_education');
            }
        });
    }

    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            foreach (['dehyar_phone','dehyar_email','dehyar_bio','dehyar_education','dehyar_experience'] as $c) {
                if (Schema::hasColumn('villages', $c)) $table->dropColumn($c);
            }
        });
    }
};
