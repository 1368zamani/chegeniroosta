<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('village_council_resolutions')) {
            Schema::create('village_council_resolutions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('session_number', 50)->nullable();
                $table->date('session_date')->nullable();
                $table->string('title', 255);
                $table->text('description')->nullable();
                $table->string('status', 30)->default('approved');
                $table->integer('progress_percent')->default(0);
                $table->decimal('budget', 15, 0)->nullable();
                $table->date('deadline')->nullable();
                $table->text('admin_note')->nullable();
                $table->string('attachment')->nullable();
                $table->boolean('is_public')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['village_id', 'status']);
                $table->index(['village_id', 'is_public']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('village_council_resolutions');
    }
};
