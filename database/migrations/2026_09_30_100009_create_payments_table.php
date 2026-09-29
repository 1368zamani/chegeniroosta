<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');

                // روش: manual (فیش دستی) / gateway (زرین‌پال، آیدی‌پی، ...)
                $table->string('method', 30);

                // برای manual
                $table->string('payer_name', 255)->nullable();
                $table->string('payer_card', 30)->nullable();
                $table->string('bank_reference', 100)->nullable();  // شماره پیگیری بانکی
                $table->text('note')->nullable();

                // برای gateway
                $table->string('gateway_name', 50)->nullable();     // zarinpal, idpay, ...
                $table->string('transaction_id', 100)->nullable();
                $table->json('gateway_payload')->nullable();

                $table->bigInteger('amount_rial');
                $table->dateTime('paid_at')->nullable();

                // وضعیت: pending_review, approved, rejected
                $table->string('status', 30)->default('pending_review');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->dateTime('reviewed_at')->nullable();
                $table->text('review_note')->nullable();

                $table->timestamps();

                $table->index(['bill_id', 'status']);
                $table->index('transaction_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
