<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payment_accounts')) {
            Schema::create('payment_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dehyari_id')->constrained('dehyaris')->onDelete('cascade');
                $table->string('bank_name', 100)->nullable();
                $table->string('account_number', 50)->nullable();
                $table->string('card_number', 30)->nullable();
                $table->string('sheba', 30)->nullable();
                $table->string('account_holder', 255)->nullable();
                $table->boolean('is_default')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['dehyari_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
