<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('fee_parameters')) {
            Schema::create('fee_parameters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fee_type_id')->constrained('fee_types')->onDelete('cascade');
                $table->string('key', 50);                  // area, units, floors, ...
                $table->string('label', 255);                // برچسب فارسی
                $table->string('input_type', 30)->default('number'); // number, text, select, boolean
                $table->json('options')->nullable();         // برای select
                $table->string('unit_label', 50)->nullable(); // مترمربع، نفر، ...
                $table->decimal('default_value', 15, 4)->nullable();
                $table->boolean('is_required')->default(true);
                $table->boolean('is_multiplier')->default(false);  // آیا در محاسبه ضرب می‌شود؟
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['fee_type_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_parameters');
    }
};
