<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dehyaris', function (Blueprint $table) {
            if (!Schema::hasColumn('dehyaris', 'photo')) {
                $table->string('photo')->nullable()->after('dehyar_name');
            }
            if (!Schema::hasColumn('dehyaris', 'bio')) {
                $table->text('bio')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('dehyaris', 'email')) {
                $table->string('email', 100)->nullable()->after('bio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dehyaris', function (Blueprint $table) {
            $table->dropColumn(['photo', 'bio', 'email']);
        });
    }
};
