<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('section_requests')) {
            Schema::create('section_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
                $table->string('tracking_code', 30)->unique();
                $table->string('full_name', 100);
                $table->string('phone', 20);
                $table->string('subject', 100);
                $table->text('description');
                $table->string('attachment')->nullable();
                $table->string('status', 30)->default('submitted');
                $table->text('admin_response')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['section_id', 'status']);
                $table->index('tracking_code');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('section_requests');
    }
};
