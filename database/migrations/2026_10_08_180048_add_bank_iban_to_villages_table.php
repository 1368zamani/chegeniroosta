<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('villages', 'bank_iban')) {
            Schema::table('villages', function (Blueprint $table) {
                $table->string('bank_iban', 50)->nullable()->after('bank_account')
                    ->comment('شماره شبا');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('villages', 'bank_iban')) {
            Schema::table('villages', function (Blueprint $table) {
                $table->dropColumn('bank_iban');
            });
        }
    }
};
