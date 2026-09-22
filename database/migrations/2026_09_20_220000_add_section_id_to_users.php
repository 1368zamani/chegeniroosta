<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'section_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('section_id')->nullable()->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'section_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }
    }
};
