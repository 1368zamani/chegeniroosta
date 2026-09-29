<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('fee_tariffs')) {
            Schema::create('fee_tariffs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dehyari_id')->constrained('dehyaris')->onDelete('cascade');
                $table->foreignId('fee_type_id')->constrained('fee_types')->onDelete('cascade');
                $table->integer('year');                          // سال شمسی: 1405
                $table->date('valid_from')->nullable();
                $table->date('valid_to')->nullable();
                $table->text('description')->nullable();

                // مبالغ پایه (به ریال)
                $table->bigInteger('base_amount')->default(0);
                $table->bigInteger('unit_rate')->default(0);       // نرخ هر واحد
                $table->decimal('coefficient', 10, 4)->default(1); // ضریب

                // فرمول: نوع محاسبه
                // fixed, area_x_rate, area_x_rate_x_coef,
                // units_x_rate, base_plus_units_x_rate, custom
                $table->string('formula_type', 50)->default('fixed');
                $table->text('formula_expression')->nullable();   // برای custom

                // واحد
                $table->string('unit', 30)->default('rial');      // rial, m2, m, percent, count, household, shop, item

                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['dehyari_id', 'year', 'is_active']);
                $table->index('fee_type_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_tariffs');
    }
};
