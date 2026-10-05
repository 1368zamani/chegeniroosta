<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_items')) return;

        Schema::create('tariff_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_set_id')->constrained('tariff_sets')->cascadeOnDelete();
            $table->foreignId('tariff_title_id')->constrained('tariff_titles')->cascadeOnDelete();

            $table->boolean('is_active')->default(true);
            $table->bigInteger('previous_amount')->default(0);
            $table->decimal('increase_percent', 5, 2)->default(0);
            $table->json('params')->nullable();
            $table->bigInteger('final_amount')->default(0);
            $table->string('unit', 30)->default('rial');
            $table->text('notes')->nullable();
            $table->string('formula_display', 500)->nullable();

            $table->timestamps();

            $table->unique(['tariff_set_id', 'tariff_title_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_items');
    }
};
