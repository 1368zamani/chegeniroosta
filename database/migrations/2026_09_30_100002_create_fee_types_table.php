<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('fee_types')) {
            Schema::create('fee_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('fee_categories')->onDelete('cascade');
                $table->string('slug', 100)->nullable();
                $table->string('name', 255);
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['category_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
