<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_staffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('position');
            $table->string('phone')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_staffs');
    }
};
