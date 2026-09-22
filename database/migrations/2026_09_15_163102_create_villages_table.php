<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();

            $table->string('county')->nullable();
            $table->string('section')->nullable();
            $table->string('district')->nullable();
            $table->string('province')->default('لرستان');

            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('population')->nullable();
            $table->string('households')->nullable();
            $table->string('area')->nullable();

            $table->string('dehyar_name')->nullable();
            $table->string('council_head_name')->nullable();

            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
