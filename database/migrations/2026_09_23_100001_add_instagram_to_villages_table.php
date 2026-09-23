<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            if (!Schema::hasColumn('villages', 'dehyar_instagram')) {
                $table->string('dehyar_instagram', 255)->nullable()->after('dehyar_hours');
            }
        });
    }
    public function down(): void
    {
        Schema::table('villages', function (Blueprint $table) {
            $table->dropColumn('dehyar_instagram');
        });
    }
};
