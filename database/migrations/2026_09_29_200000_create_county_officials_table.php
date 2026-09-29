<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('county_officials')) {
            Schema::create('county_officials', function (Blueprint $table) {
                $table->id();
                $table->string('type', 30); // governor, deputy
                $table->string('name', 255);
                $table->string('position', 100);
                $table->string('photo')->nullable();
                $table->text('bio')->nullable();
                $table->text('education')->nullable();
                $table->text('experience')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['type', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('county_officials');
    }
};
