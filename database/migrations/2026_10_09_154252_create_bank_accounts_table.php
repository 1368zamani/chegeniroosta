<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('village_id')->constrained('villages')->cascadeOnDelete();
            $table->string('bank_name', 100);               // بانک ملی، پست بانک
            $table->string('account_holder', 200);           // صاحب حساب
            $table->string('iban', 30)->nullable();          // شبا
            $table->string('card_number', 20)->nullable();   // شماره کارت
            $table->string('account_number', 30)->nullable(); // شماره حساب
            $table->boolean('is_default')->default(true);    // حساب پیش‌فرض
            $table->boolean('is_active')->default(true);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['village_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
