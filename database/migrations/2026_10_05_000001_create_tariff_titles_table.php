<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_titles')) return;

        Schema::create('tariff_titles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->enum('type', ['levy', 'service_fee', 'penalty']);
            $table->string('name_fa', 255);
            $table->string('formula_key', 50)->nullable();
            $table->text('statutory_note')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_titles');
    }
};
