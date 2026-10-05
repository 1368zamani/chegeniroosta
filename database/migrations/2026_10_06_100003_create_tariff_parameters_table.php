<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_parameters')) return;

        Schema::create('tariff_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_set_id')->constrained('tariff_sets')->cascadeOnDelete();
            $table->string('key', 60);
            $table->string('value', 255)->nullable();
            $table->string('unit', 30)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tariff_set_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_parameters');
    }
};
