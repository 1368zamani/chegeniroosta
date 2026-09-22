<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('name');                   // نام امکانات
            $table->string('icon')->nullable();       // آیکون
            $table->string('category')->nullable();   // دسته (آموزشی، بهداشتی، ...)
            $table->boolean('is_available')->default(true); // دارد/ندارد
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_facilities');
    }
};
