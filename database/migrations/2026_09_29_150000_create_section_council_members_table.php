<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('section_council_members')) {
            Schema::create('section_council_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
                $table->string('name', 255);
                $table->string('position', 100);
                $table->string('phone', 20)->nullable();
                $table->string('photo')->nullable();
                $table->text('bio')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['section_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('section_council_members');
    }
};
