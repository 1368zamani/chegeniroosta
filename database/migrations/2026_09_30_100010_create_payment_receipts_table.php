<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payment_receipts')) {
            Schema::create('payment_receipts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
                $table->string('file_path');
                $table->string('original_name', 255)->nullable();
                $table->string('mime_type', 100)->nullable();
                $table->bigInteger('size')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
