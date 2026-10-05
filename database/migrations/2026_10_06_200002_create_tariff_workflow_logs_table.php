<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tariff_workflow_logs')) return;

        Schema::create('tariff_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_set_id')->constrained('tariff_sets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->string('user_role', 40)->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('note')->nullable();
            $table->json('context')->nullable();

            $table->timestamps();

            $table->index(['tariff_set_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tariff_workflow_logs');
    }
};
