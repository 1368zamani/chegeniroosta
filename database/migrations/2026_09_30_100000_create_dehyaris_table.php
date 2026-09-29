<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('dehyaris')) {
            Schema::create('dehyaris', function (Blueprint $table) {
                $table->id();
                $table->foreignId('village_id')->constrained('villages')->onDelete('cascade');
                $table->string('name', 255);              // نام دهیاری
                $table->string('dehyari_code', 50)->nullable(); // شناسه دهیاری
                $table->string('dehyar_name', 255)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('address', 500)->nullable();
                $table->string('payment_note', 500)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique('village_id');
                $table->index('is_active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dehyaris');
    }
};
