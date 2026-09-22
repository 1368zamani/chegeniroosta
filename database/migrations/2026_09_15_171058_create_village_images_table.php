<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_images', function (Blueprint $table) {

            $table->id();

            $table->foreignId('village_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title')->nullable();

            $table->string('image');

            $table->text('description')
                ->nullable();

            $table->boolean('is_cover')
                ->default(false);

            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('village_images');
    }
};
