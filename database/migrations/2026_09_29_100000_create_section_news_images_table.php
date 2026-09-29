<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('section_news_images')) {
            Schema::create('section_news_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('news_id')->constrained('section_news')->onDelete('cascade');
                $table->string('image');
                $table->string('caption')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->index(['news_id', 'sort_order']);
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('section_news_images');
    }
};
