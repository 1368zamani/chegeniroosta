<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('bill_items')) {
            Schema::create('bill_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
                $table->string('title', 255);
                $table->string('description', 500)->nullable();
                $table->bigInteger('amount_rial');              // مبلغ این آیتم به ریال
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
