<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'officer_type')) {
                // نوع کارشناس: financial یا technical
                $table->string('officer_type', 20)->nullable()->after('role');
                $table->string('phone', 20)->nullable()->after('officer_type');
                $table->string('position', 100)->nullable()->after('phone');
                $table->boolean('is_active')->default(true)->after('position');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['officer_type', 'phone', 'position', 'is_active']);
        });
    }
};
