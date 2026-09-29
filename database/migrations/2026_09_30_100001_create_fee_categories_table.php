<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('fee_categories')) {
            Schema::create('fee_categories', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 50)->unique();     // construction, waste, business
                $table->string('name', 255);
                $table->string('icon', 20)->nullable();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_categories');
    }
};
