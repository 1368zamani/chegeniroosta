<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('planned'); // planned/in_progress/done/paused
            $table->integer('progress')->default(0);      // 0-100
            $table->string('contractor')->nullable();     // دستگاه مجری
            $table->string('budget')->nullable();         // اعتبار
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('before_image')->nullable();
            $table->string('after_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_projects');
    }
};
