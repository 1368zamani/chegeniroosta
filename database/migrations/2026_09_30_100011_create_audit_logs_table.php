<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('action', 50);              // create, update, delete, approve, reject, pay
                $table->string('entity_type', 100);        // App\Models\Bill, ...
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->json('changes')->nullable();       // {"before": {...}, "after": {...}}
                $table->bigInteger('amount_rial')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['entity_type', 'entity_id']);
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
